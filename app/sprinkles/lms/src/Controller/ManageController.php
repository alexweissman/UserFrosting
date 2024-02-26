<?php

namespace UserFrosting\Sprinkle\Lms\Controller;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;

use UserFrosting\Sprinkle\Lms\Database\Models\League;
use UserFrosting\Sprinkle\Lms\Database\Models\RoundUser;
use UserFrosting\Sprinkle\Lms\Database\Models\Gameweek;
use UserFrosting\Sprinkle\Lms\Database\Models\Team;
use UserFrosting\Sprinkle\Lms\Database\Models\Pick;
use UserFrosting\Sprinkle\Lms\Database\Models\Fixture;
use UserFrosting\Sprinkle\Lms\Database\Models\Round;

class ManageController extends SimpleController
{

    public function PageManageLeagues(Request $request, Response $response, $args)
    {  
        $authorizer = $this->ci->authorizer;
        $currentUser = $this->ci->currentUser;
        if (!$authorizer->checkAccess($currentUser, 'fk_manager')) {
            throw new ForbiddenException();
        }
        
        $leagues = League::select('league.id', 'league.league_name', 'users.user_name')
                ->join('users', 'users.id', 'league.admin_user_id')
                ->get();

        return $this->ci->view->render($response, 'pages/pageManageLeagues.html.twig',[
            'leagues' => $leagues
        ]);
    }

    public function PageManageLeague(Request $request, Response $response, $args)
    {  
        $authorizer = $this->ci->authorizer;
        $currentUser = $this->ci->currentUser;
        if (!$authorizer->checkAccess($currentUser, 'fk_manager')) {
            throw new ForbiddenException();
        }
        
        $league = League::where('league.id', $args['league_id'])
            ->join('users', 'league.admin_user_id', 'users.id')
            ->first();

        return $this->ci->view->render($response, 'pages/pageManageLeague.html.twig',[
            'league' => $league
        ]);
    }

    ///////////////// old
    public function pageRollOnGameweekChosen(Request $request, Response $response, $args)
    {  
        $authorizer = $this->ci->authorizer;
        $currentUser = $this->ci->currentUser;
        if (!$authorizer->checkAccess($currentUser, 'fk_manager')) {
            throw new ForbiddenException();
        }
        
        
        $gameweek = Gameweek::where('id', $args['gameweek_id'])->first();
        $fixtures = Fixture::where('gameweek_id', $gameweek->id)->get();
        $teams = Team::get();
        $picks = Pick::where('gameweek_id', $gameweek->id)
            ->leftJoin('users', 'users.id', 'pick.user_id')
            ->leftJoin('round_user', 'round_user.id', 'pick.round_user_id')
            ->leftJoin('round', 'round_user.round_id', 'round.id')
            ->leftJoin('league', 'round.league_id', 'league.id')
            ->get();
        
        return $this->ci->view->render($response, 'pages/pageRollOnGameweekChosen.html.twig',[
            'gameweek' => $gameweek,
            'fixtures' => $fixtures,
            'teams' => $teams,
            'picks' => $picks
        ]);
    }
    public function RollOnGameweek(Request $request, Response $response, $args)
    {  
        $authorizer = $this->ci->authorizer;
        $currentUser = $this->ci->currentUser;
        if (!$authorizer->checkAccess($currentUser, 'fk_manager')) {
            throw new ForbiddenException();
        }

        $gameweek = Gameweek::where('id', $args['gameweek_id'])->first();
        // implement the calculated logic
        $picks = Pick::where('gameweek_id', $gameweek->id)
            ->join('users', 'users.id', 'pick.user_id')
            ->join('round_user', 'round_user.id', 'pick.round_user_id')
            ->join('round', 'round_user.round_id', 'round.id')
            ->join('league', 'round.league_id', 'league.id')
            ->select('pick.id', 'pick.team_id', 'pick.round_user_id')
            ->get();
        //// Deal with Picks
        // for each pick
        foreach($picks as $pick){
            // check the result for their fixture
            
            $fixture = Fixture::where('home_team', $pick->team_id)
                    ->where('gameweek_id', $gameweek->id)
                    ->orWhere('away_team', $pick->team_id)
                    ->where('gameweek_id', $gameweek->id)
                    ->first();
            $round_user = RoundUser::where('id', $pick->round_user_id)->first();
            if($fixture->result == $pick->team_id){
            // win
                // update pick result to won
                $pick->result = 'won';
                $pick->save();
            }else{
            // loss/draw
                // update pick to loss
                $pick->result = 'loss';
                $pick->save();
                // update round_user status to out
                $round_user->user_status = 'out';
                $round_user->save();
            }
        }

        // move current round gameweeks forwards
        $gameweek = Gameweek::where('id', $args['gameweek_id'])->first();
        $nextGameweek = Gameweek::where('id', ($gameweek->id + 1))->first();
        $round = Round::where('current_gameweek', $gameweek->id)->get();
        foreach($round as $r){ 
            $r->current_gameweek = $nextGameweek->id;
            $r->save();
            $this->checkForRoundEnd($r->id);
        }

        ////Mark Gameweek Complete
        $gameweek->status = 'complete';
        $gameweek->save();

        $nextGameweek->status = 'in progress';
        $nextGameweek->save();
        
        ////Deal with Leagues

        $gameweek = Gameweek::where('id', $args['gameweek_id'])->first();
        $fixtures = Fixture::where('gameweek_id', $gameweek->id)->get();
        $teams = Team::get();
        $picks = Pick::where('gameweek_id', $gameweek->id)
            ->leftJoin('users', 'users.id', 'pick.user_id')
            ->leftJoin('round_user', 'round_user.id', 'pick.round_user_id')
            ->leftJoin('round', 'round_user.round_id', 'round.id')
            ->leftJoin('league', 'round.league_id', 'league.id')
            ->get();
        
        return $this->ci->view->render($response, 'pages/pageRollOnGameweekChosen.html.twig',[
            'gameweek' => $gameweek,
            'fixtures' => $fixtures,
            'teams' => $teams,
            'picks' => $picks
        ]);
    }


    function checkForRoundEnd($round_id){
            $round = Round::where('id', $round_id)->first();    
            //loop throung each round user, if only 1 or none has status of active then change to won & update round status - finished
            $round_users = RoundUser::where('round_id', $round->id)->get();
            $number_of_active_players = 0;
            foreach($round_users as $ru){
                if($ru->user_status == 'active'){
                    $number_of_active_players++;
                }
            }
           if($number_of_active_players == 1){
                //one letft - they've won
                //update round_user status to 'won'
                $won_user = RoundUser::where('round_id', $round->id)
                    ->where('user_status', 'active')
                    ->first();
                $won_user->user_status = 'won';
                $won_user->save();
            }
    }


// ------------
  
    public function pageMakePick(Request $request, Response $response, $args)
    {  
        $league = League::where('id', $args['league_id'])->first();
        //check if the league exists
        if ($league){
            //check they're active to make a pick e.g. not out, or not even part of the league
            $roundUser = Round::where('league_id', $league->id)
                        ->where('round.status', 'active')
                        ->where('round_user.user_id', $this->ci->currentUser->id)
                        ->where('round_user.user_status', 'active')
                        ->leftJoin('round_user', 'round_user.round_id', '=' , 'round.id')
                        ->first();
            if($roundUser){
                //see if they have a current pick for the gameweek
                $currentPick = Pick::where('user_id', $this->ci->currentUser->id)
                        ->where('round_user_id', $roundUser->id)
                        ->where('gameweek_id', $roundUser->current_gameweek)
                        ->where('pick.result', 'pending')
                        ->first();
                $previousPicks = Pick::where('user_id', $this->ci->currentUser->id)
                        ->where('round_user_id', $roundUser->id)
                        ->where('result', '<>', 'pending')
                        ->get();
                $fixtures = Fixture::where('gameweek_id', $roundUser->current_gameweek)
                        ->get();
                $teams = Team::orderBy('team_name', 'asc')
                        ->get();
                return $this->ci->view->render($response, 'pages/pageMakePick.html.twig', [
                    'currentPick' => $currentPick,
                    'previousPicks' => $previousPicks,
                    'league' => $league,
                    'teams' => $teams,
                    'fixtures' => $fixtures
                ]);
            }
        }
        // not eligible to make a pick 
        $ms = $this->ci->alerts;
        $ms->addMessage('danger', "Sorry, you're not eligible to make a pick for this league now." );
        return $this->ci->view->render($response, 'pages/pageMakePick.html.twig');
    }

    //create/update pick
    public function makeNewPick(Request $request, Response $response, $args)
    {
        $league = League::where('id', $args['league_id'])->first();
        //check if the league exists
        if ($league){
            //check they're active to make a pick e.g. not out, or not even part of the league
            $roundUser = Round::where('league_id', $league->id)
                        ->where('round.status', 'active')
                        ->where('round_user.user_id', $this->ci->currentUser->id)
                        ->where('round_user.user_status', 'active')
                        ->leftJoin('round_user', 'round_user.round_id', '=' , 'round.id')
                        ->select('*','round_user.id as round_user_id')
                        ->first();
            if($roundUser){
                $params = $request->getParsedBody();
                //see if they have a current pick for the gameweek
                $currentPick = Pick::where('user_id', $this->ci->currentUser->id)
                        ->where('round_user_id', $roundUser->id)
                        ->where('gameweek_id', $roundUser->current_gameweek)
                        ->first();
                if($currentPick){
                    //already picked, making new one
                    $currentPick->team_id = $params['pickedTeam'];
                    $currentPick->save();
                }else{
                    //create new pick for the gameweek
                    $newPick = new Pick([
                        'gameweek_id' => $roundUser->current_gameweek,
                        'round_user_id' => $roundUser->round_user_id,
                        'team_id' => $params['pickedTeam'],
                        'user_id' => $this->ci->currentUser->id,
                        'result' => 'pending'
                    ]);
                    $newPick->save();
                }
            }
        }
        
        $config = $this->ci->config;
        $url = $config['site.uri.public'] . '/leagues';
        header( "Location: $url" );
        exit();
    }
}

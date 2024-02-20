<?php

namespace UserFrosting\Sprinkle\Lms\Controller;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Sprinkle\Core\Util\EnvironmentInfo;
use UserFrosting\Support\Exception\ForbiddenException;

use UserFrosting\Sprinkle\Lms\Database\Models\League;
use UserFrosting\Sprinkle\Lms\Database\Models\RoundUser;
use UserFrosting\Sprinkle\Lms\Database\Models\Gameweek;
use UserFrosting\Sprinkle\Lms\Database\Models\Team;
use UserFrosting\Sprinkle\Lms\Database\Models\Pick;
use UserFrosting\Sprinkle\Lms\Database\Models\Fixture;
use UserFrosting\Sprinkle\Lms\Database\Models\Round;

class PickController extends SimpleController
{ 
    public function pageMakePick(Request $request, Response $response, $args)
    {  
        $league = League::where('id', $args['league_id'])->first();
        //check if the league exists
        if ($league){
            $deadline = Round::where('round.status', 'active')
                    ->where('league_id', $league->id)
                    ->leftJoin('gameweek', 'round.current_gameweek', 'gameweek.id')
                    ->first();
            if($deadline->deadline > date('Y-m-d H:i:s')){
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
                    $gameweek = Gameweek::where('id', $roundUser->current_gameweek)->first();
                    $fixtures = Fixture::where('gameweek_id', $gameweek->id)
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
        }
        // not eligible to make a pick 
        $ms = $this->ci->alerts;
        $ms->addMessage('danger', "Sorry, you're not eligible to make a pick for this league now." );
        return $this->ci->view->render($response, 'pages/pageMakePick.html.twig');
    }

    //create/update pick
    public function makeNewPick(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
        $ms = $this->ci->alerts;
        if(!$params['pickedTeam']){
            $ms->addMessage('danger', "Please choose a team and press 'Confirm Pick'." );
            $config = $this->ci->config;
            $url = $config['site.uri.public'] . '/pick/' . $args['league_id'];
            header( "Location: $url" );
            exit(); 
        }
        // Check that the team exists
        $team = Team::where('id', $params['pickedTeam'])->first();
        if(!$team){
            $ms->addMessage('danger', "Team does not exist." );
            $config = $this->ci->config;
            $url = $config['site.uri.public'] . '/pick/' . $args['league_id'];
            header( "Location: $url" );
            exit();
        }
        
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
        
        $leaguesPage = $this->ci->router->pathFor('leagues');
        return $response->withRedirect($leaguesPage);
    }
}

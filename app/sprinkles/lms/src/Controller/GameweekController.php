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

class GameweekController extends SimpleController
{

    public function pageRollOnGameweek(Request $request, Response $response, $args)
    {
        $authorizer = $this->ci->authorizer;
        $currentUser = $this->ci->currentUser;
        if (!$authorizer->checkAccess($currentUser, 'fk_manager')) {
            throw new ForbiddenException();
        }


        // choose gameweek
        $gameweeks = Gameweek::get();
        return $this->ci->view->render($response, 'pages/pageRollOnGameweek.html.twig', [
            'gameweeks' => $gameweeks
        ]);
    }
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

        return $this->ci->view->render($response, 'pages/pageRollOnGameweekChosen.html.twig', [
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

        // Add No Pick team to those who didn't make a pick
        // loop through round users,
        //      Issue - adding nopick to everyone
        $rounds_np = Round::where('current_gameweek', $gameweek->id)
            ->select('id')
            ->get();
        $round_users_np = RoundUser::whereIn('round_id', $rounds_np)
            ->select('id', 'user_id')
            ->get();
        $noPickTeam = Team::where('team_name', 'No Pick')->first();
        foreach ($round_users_np as $r) {
            $p = Pick::where('gameweek_id', $gameweek->id)
                ->where('user_id', $r->user_id)
                ->first();
            // if they don't have a pick
            if (!$p) {
                // New pick - No Pick team
                $np = new Pick([
                    'gameweek_id' => $gameweek->id,
                    'round_user_id' => $r->id,
                    'team_id' => $noPickTeam->id,
                    'user_id' => $currentUser->id,
                    'result' => 'lost'
                ]);
                $np->save();
                // mark the round_user as out
                $ru = RoundUser::where('id', $r->id)->first();
                $ru->user_status = 'out';
                $ru->save();
            }
        }
        // End Adding No Pick


        foreach ($picks as $pick) {
            // check the result for their fixture
            $fixture = Fixture::where('home_team', $pick->team_id)
                ->where('gameweek_id', $gameweek->id)
                ->orWhere('away_team', $pick->team_id)
                ->where('gameweek_id', $gameweek->id)
                ->first();
            $round_user = RoundUser::where('id', $pick->round_user_id)->first();
            if ($fixture->result == $pick->team_id) {
                // win
                // update pick result to won
                $pick->result = 'won';
                $pick->save();
            } else {
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
        foreach ($round as $r) {
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

        return $this->ci->view->render($response, 'pages/pageRollOnGameweekChosen.html.twig', [
            'gameweek' => $gameweek,
            'fixtures' => $fixtures,
            'teams' => $teams,
            'picks' => $picks
        ]);
    }


    function checkForRoundEnd($round_id)
    {
        $round = Round::where('id', $round_id)->first();
        //loop throung each round user, if only 1 or none has status of active then change to won & update round status - finished
        $round_users = RoundUser::where('round_id', $round->id)->get();
        $number_of_active_players = 0;
        foreach ($round_users as $ru) {
            if ($ru->user_status == 'active') {
                $number_of_active_players++;
            }
        }
        if ($number_of_active_players == 1) {
            //one letft - they've won
            //update round_user status to 'won'
            $won_user = RoundUser::where('round_id', $round->id)
                ->where('user_status', 'active')
                ->first();
            $won_user->user_status = 'won';
            $won_user->save();
        }
    }
}

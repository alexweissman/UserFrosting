<?php

namespace UserFrosting\Sprinkle\Lms\Controller;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;

use UserFrosting\Sprinkle\Lms\Database\Models\League;
use UserFrosting\Sprinkle\Lms\Database\Models\LeagueUser;
use UserFrosting\Sprinkle\Lms\Database\Models\Gameweek;
use UserFrosting\Sprinkle\Lms\Database\Models\Team;
use UserFrosting\Sprinkle\Lms\Database\Models\Pick;
use UserFrosting\Sprinkle\Lms\Database\Models\Fixture;
use UserFrosting\Sprinkle\Lms\Database\Models\Round;
use UserFrosting\Sprinkle\Lms\Database\Models\RoundUser;

use UserFrosting\Sprinkle\Account\Database\Models\User;

class LeagueController extends SimpleController
{

    public function pageLeagues(Request $request, Response $response, $args)
    {
        $leagues = RoundUser::where('round_user.user_id', '=', $this->ci->currentUser->id)
            ->whereIn('round.status', ['active', 'complete'])
            ->leftJoin('round', 'round_user.round_id', '=', 'round.id')
            ->leftJoin('league', 'round.league_id', '=', 'league.id')
            ->leftJoin('pick', function ($join) {
                $join->on('pick.user_id', 'round_user.user_id')
                    ->whereColumn('pick.round_user_id', 'round_user.id')
                    ->where('pick.gameweek_id', '=', 'round.current_gameweek');
            })
            ->leftJoin('gameweek', 'round.current_gameweek', 'gameweek.id')
            ->select('round.status as rnd_status', 'round_user.*', 'round.*', 'league.*', 'pick.*', 'gameweek.*')
            ->get();
        $current_picks = Pick::where('pick.user_id', $this->ci->currentUser->id)
            ->leftJoin('round_user', 'pick.round_user_id', 'round_user.id')
            ->leftJoin('round', 'round_user.round_id', 'round.id')
            ->leftJoin('team', 'pick.team_id', 'team.id')
            ->where('round.status', 'active')
            ->where('pick.result', 'pending')
            ->get();

        $total_players = RoundUser::selectRaw("round_id, count(round_id) as 'count'")
            ->groupBy('round_user.round_id')
            ->get();
        $remaining_players = RoundUser::selectRaw("round_id, count(round_id) as 'count'")
            ->whereIn('user_status', ['active', 'won'])
            ->groupBy('round_user.round_id')
            ->get();

        return $this->ci->view->render($response, 'pages/pageLeagues.html.twig', [
            'leagues' => $leagues,
            'current_picks' => $current_picks,
            'total_players' => $total_players,
            'remaining_players' => $remaining_players
        ]);
    }

    public function pageIndex(Request $request, Response $response, $args)
    {
        return $this->ci->view->render($response, 'pages/index.html.twig');
    }

    public function pageCreateLeague(Request $request, Response $response, $args)
    {
        $gameweeks = Gameweek::whereIn('status', ['pending', 'in progress'])->get();
        return $this->ci->view->render($response, 'pages/pageCreateLeague.html.twig', [
            'gameweeks' => $gameweeks
        ]);
    }

    public function pageJoinLeague(Request $request, Response $response, $args)
    {
        return $this->ci->view->render($response, 'pages/pageJoinLeague.html.twig');
    }

    public function pageViewLeague(Request $request, Response $response, $args)
    {
        $league = League::where('id', $args['league_id'])->first();
        if (!$league) {
            $ms = $this->ci->alerts;
            $ms->addMessage('danger', "League does not exist.");

            $leaguesPage = $this->ci->router->pathFor('leagues');
            return $response->withRedirect($leaguesPage);
        }

        $round = Round::where('league_id', $league->id)
            ->where('status', 'active')
            ->first();

        $round_users = RoundUser::where('round_id', $round->id)
            ->where('user_id', $this->ci->currentUser->id)
            ->leftJoin('users', 'users.id', 'round_user.user_id')
            ->get();

        // Check that they belong to the league
        if (count($round_users) === 0) {
            $ms = $this->ci->alerts;
            $ms->addMessage('danger', "You don't belong to this league.");

            $config = $this->ci->config;
            $url = $config['site.uri.public'] . '/leagues';
            header("Location: $url");
            exit();
        }

        $round_users = RoundUser::where('round_id', $round->id)
            ->leftJoin('users', 'users.id', 'round_user.user_id')
            ->get();


        $picks = Pick::where('round.id', $round->id)
            ->leftJoin('round_user', 'round_user.id', 'pick.round_user_id')
            ->leftJoin('round', 'round.id', 'round_user.round_id')
            ->get();
        $gameweeks = Gameweek::get();
        $teams = Team::get();
        return $this->ci->view->render($response, 'pages/pageViewLeague.html.twig', [
            'league' => $league,
            'round_users' => $round_users,
            'picks' => $picks,
            'gameweeks' => $gameweeks,
            'teams' => $teams
        ]);
    }

    public function joinLeague(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
        $league = League::where('join_code', $params['league_joining_code'])->first();
        //check league exists
        if ($league) {
            $round = Round::where('league_id', $league->id)->first();
            //check that not already a member of the league
            $roundUsers = RoundUser::where('round_id', $round->id)->where('user_id', $this->ci->currentUser->id)->first();
            if ($roundUsers) {
                //user is being a twat joining a league they're already in
                $ms = $this->ci->alerts;
                $ms->addMessage('danger', "Already a member of that league.");
            } else {
                $round = Round::where('league_id', $league->id)->where('status', 'active')->first(); // only one round should be active per league
                // add user to the rounduser
                $roundUser = new RoundUser([
                    'user_id' => $this->ci->currentUser->id,
                    'round_id' => $round->id,
                    'user_status' => 'active'
                ]);
                $roundUser->save();
                $ms = $this->ci->alerts;
                $ms->addMessage('success', "Joined league.");
            }
        } else {
            //league joining code not found
            $ms = $this->ci->alerts;
            $ms->addMessage('danger', "Incorrect league joining code.");
        }
        $leaguesPage = $this->ci->router->pathFor('leagues');
        return $response->withRedirect($leaguesPage);
    }
    public function joinLeagueByLink(Request $request, Response $response, $args)
    {
        $league = League::where('join_code', $args['joining_code'])->first();
        //check league exists
        if ($league) {
            $round = Round::where('league_id', $league->id)->first();
            //check that not already a member of the league
            $roundUsers = RoundUser::where('round_id', $round->id)->where('user_id', $this->ci->currentUser->id)->first();
            if ($roundUsers) {
                //user is being a twat joining a league they're already in
                $ms = $this->ci->alerts;
                $ms->addMessage('danger', "Already a member of that league.");
            } else {
                $round = Round::where('league_id', $league->id)->where('status', 'active')->first(); // only one round should be active per league
                // add user to the rounduser
                $roundUser = new RoundUser([
                    'user_id' => $this->ci->currentUser->id,
                    'round' => $round->id,
                    'user_status' => 'active'
                ]);
                $roundUser->save();
                $ms = $this->ci->alerts;
                $ms->addMessage('success', "Joined league.");
            }
        } else {
            //league joining code not found
            $ms = $this->ci->alerts;
            $ms->addMessage('danger', "Incorrect league joining code.");
        }

        $config = $this->ci->config;
        $url = $config['site.uri.public'] . '/leagues/join-league';
        header("Location: $url");
        exit();
    }

    public function createLeague(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
        $gameweek = Gameweek::where('gameweek_number', (int)$params['start_gameweek'])->first();
        $newLeague = new League([
            'league_name' => $params['league_name'],
            'admin_user_id' => $this->ci->currentUser->id,
            'join_code' => $this->generateRandomString()
        ]);
        $newLeague->save();

        $newRound = new Round([
            'start_gameweek' => (int)$params['start_gameweek'],
            'current_gameweek' => (int)$params['start_gameweek'],
            'league_id' => $newLeague->id,
            'status' => 'active'
        ]);
        $newRound->save();

        $newRoundUser = new RoundUser([
            'user_id' => $this->ci->currentUser->id,
            'round_id' => $newRound->id,
            'user_status' => 'active'
        ]);
        $newRoundUser->save();

        $ms = $this->ci->alerts;
        $ms->addMessage('success', "League '" . $newLeague['league_name'] . "' created.");

        $leaguesPage = $this->ci->router->pathFor('leagues');
        return $response->withRedirect($leaguesPage);
    }

    private function generateRandomString()
    {
        $characters = '2345679abcdefghjkmnpqrstuvwxyzACDEFGHJKMNPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function pageCreateNewRound(Request $request, Response $response, $args)
    {
        $error = false;
        $league = League::where('id', $args['league_id'])->first();

        // Check if league exists
        if (!$league) {
            $error = true;
        } else {
            // check if are owner of the league
            if ($league->admin_user_id != $this->ci->currentUser->id) {
                $error = true;
            } else {
                // Get gameweeks and return the page
                $gameweeks = Gameweek::whereIn('status', ['pending', 'in progress'])->get();
                return $this->ci->view->render($response, 'pages/pageCreateNewRound.html.twig', [
                    'gameweeks' => $gameweeks,
                    'league' => $league
                ]);
            }
        }

        $ms = $this->ci->alerts;
        $ms->addMessage('danger', "This leage cannot have a new Round created");

        $leaguesPage = $this->ci->router->pathFor('leagues');
        return $response->withRedirect($leaguesPage);
    }

    /*
    * /leagues/new-round - POST 
    * Receives league_id, gameweek_id, in PARAMS
    * What - Creates new round for the league and closes off the current round.
    * Why - Done because a Round has ended for the league.
    */
    public function createNewRound(Request $request, Response $response, $args)
    {
        $error = false;
        $params = $request->getParsedBody();
        $league = League::where('id', $params['league_id'])->first();
        $ms = $this->ci->alerts;

        // Check if league exists
        if (!$league) {
            $error = true;
        } else {
            // check if are owner of the league
            if ($league->admin_user_id != $this->ci->currentUser->id) {
                $error = true;
            } else {
                // Get current round and set status to 'ended'
                $round = Round::where('league_id', $league->id)
                    ->where('status', 'active')->first(); // because only one active Round per League at a time
                $round->status = 'ended';
                $round->save();

                // Create new round and set status to 'active'
                $newRound = new Round([
                    'start_gameweek' => $params['start_gameweek'],
                    'league_id' => $league->id,
                    'status' => 'active',
                    'current_gameweek' => $params['start_gameweek']
                ]);
                $newRound->save();

                // Create new round_user for each user that played in the previous round
                $roundUsers = RoundUser::where('round_id', $round->id)->get();
                foreach ($roundUsers as $ru) {
                    $newRoundUser = new RoundUser([
                        'user_id' => $ru->user_id,
                        'round_id' => $newRound->id,
                        'status' => 'active'
                    ]);
                    $newRoundUser->save();
                }
                $ms->addMessage('success', "New Round created");
                $leaguesPage = $this->ci->router->pathFor('leagues');
                return $response->withRedirect($leaguesPage);
            }
        }

        $ms->addMessage('danger', "This leage cannot have a new Round created");

        $leaguesPage = $this->ci->router->pathFor('leagues');
        return $response->withRedirect($leaguesPage);
    }
}

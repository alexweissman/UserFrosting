<?php

namespace UserFrosting\Sprinkle\Lms\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;

use UserFrosting\Sprinkle\Lms\Database\Models\League;
use UserFrosting\Sprinkle\Lms\Database\Models\Gameweek;
use UserFrosting\Sprinkle\Lms\Database\Models\Team;
use UserFrosting\Sprinkle\Lms\Database\Models\Pick;
use UserFrosting\Sprinkle\Lms\Database\Models\Round;
use UserFrosting\Sprinkle\Lms\Database\Models\RoundUser;
use UserFrosting\Sprinkle\Lms\Database\Models\MarketingConsent;
use UserFrosting\Sprinkle\Lms\Database\Models\Subscription;
 
use KlaviyoAPI\KlaviyoAPI;

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
            $gameweeks = Gameweek::whereIn('status', ['pending', 'in progress'])->get();

        return $this->ci->view->render($response, 'pages/pageLeagues.html.twig', [
            'leagues' => $leagues,
            'current_picks' => $current_picks,
            'total_players' => $total_players,
            'remaining_players' => $remaining_players,
            'gameweeks' => $gameweeks
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
            ->select('users.*', 'round_user.*') 
            ->get();


        $picks = Pick::where('round.id', $round->id)
            ->leftJoin('round_user', 'round_user.id', 'pick.round_user_id')
            ->leftJoin('round', 'round.id', 'round_user.round_id')
            ->where('pick.gameweek_id' ,'!=', $round->current_gameweek)
            ->get();

        $previousRounds = Round::where('league_id', $league->id)
            ->leftJoin('round_user', 'round.id', 'round_user.round_id')
            ->leftJoin('users', 'round_user.user_id', 'users.id')
            ->where('round_user.user_status', 'won')
            ->where('round.status', 'ended')
            ->get();
        
        $gameweeks = Gameweek::get();
        $teams = Team::get();
        $subscriptions = Subscription::get();
        return $this->ci->view->render($response, 'pages/pageViewLeague.html.twig', [
            'league' => $league,
            'round_users' => $round_users,
            'picks' => $picks,
            'gameweeks' => $gameweeks,
            'teams' => $teams,
            'round' => $round,
            'previous_rounds' => $previousRounds,
            'subscriptions' => $subscriptions
        ]);
    }

    // expecting round_user ID to mark as paid
    public function markPaidEntryFee(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
        // ERROR not finding round user - is it being given theright round user
        $round_user = RoundUser::where('id', $params['round_user_id'])->first();
        if($round_user){
            $round_user->paid_entry_fee = true;
            $round_user->save();

            $round = Round::where('id', $round_user->round_id)->first();
            $league = League::where('id', $round->league_id)->first();
            $config = $this->ci->config;
            $url = $config['site.uri.public'] . '/leagues/view-league/' . $league->id;
            header("Location: $url");
            exit();
        }else{
            $leaguesPage = $this->ci->router->pathFor('leagues');
            return $response->withRedirect($leaguesPage);
        }
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


                $klaviyo = new KlaviyoAPI(
                    'pk_79382b5640e6efb5387e0d28362b6ed032', 
                    $num_retries = 3, 
                    $wait_seconds = 3,
                    $guzzle_options = [],
                    $user_agent_suffix = "/FootballKnockout");

                $consent = MarketingConsent::where('user_id', $this->ci->currentUser->id)->first();

                // Klaviyo send joined_league event
                $eventData = [
                    "data" => [
                        "type" => "event",
                        "attributes" => [
                            "properties" => [],
                            "metric" => [
                                "data" => [
                                    "type" => "metric",
                                    "attributes" => [
                                        "name" => "Joined League"
                                    ]
                                ]
                            ],
                            "profile" => [
                                "data" => [
                                    "type" => "profile",
                                    "id" => $consent->klaviyo_id,
                                    "attributes" => [],
                                ]
                            ]
                        ]
                    ]
                ];
                $klaviyo->Events->createEvent($eventData);


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
        // If signed in, join league and redirect to leagues
        if($this->ci->currentUser->id > 0){
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

                    $klaviyo = new KlaviyoAPI(
                        'pk_79382b5640e6efb5387e0d28362b6ed032', 
                        $num_retries = 3, 
                        $wait_seconds = 3,
                        $guzzle_options = [],
                        $user_agent_suffix = "/FootballKnockout");
    
                    $consent = MarketingConsent::where('user_id', $this->ci->currentUser->id)->first();
    
                    // TO DO - Klaviyo send joined_league event
                    $eventData = [
                        "data" => [
                            "type" => "event",
                            "attributes" => [
                                "properties" => [],
                                "metric" => [
                                    "data" => [
                                        "type" => "metric",
                                        "attributes" => [
                                            "name" => "Joined League"
                                        ]
                                    ]
                                ],
                                "profile" => [
                                    "data" => [
                                        "type" => "profile",
                                        "id" => $consent->klaviyo_id,
                                        "attributes" => [],
                                    ]
                                ]
                            ]
                        ]
                    ];
                    $klaviyo->Events->createEvent($eventData);
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

        }else{
            // not signed in, so show them the landing page
            $league = League::where('join_code', $args['joining_code'])->first();

            if(!$league){
                // League doesn't exist, redirect to sign in page
                $leaguesPage = $this->ci->router->pathFor('leagues');
                return $response->withRedirect($leaguesPage);
            }

            return $this->ci->view->render($response, 'pages/pageAutoJoin.html.twig', [
                'league' => $league
            ]);    
        }
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
            'status' => 'active',
            'entry_fee' => (int)$params['entry_fee']
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

        // TO DO - Klaviyo send created_league event
        $klaviyo = new KlaviyoAPI(
            'pk_79382b5640e6efb5387e0d28362b6ed032', 
            $num_retries = 3, 
            $wait_seconds = 3,
            $guzzle_options = [],
            $user_agent_suffix = "/FootballKnockout");

        $consent = MarketingConsent::where('user_id', $this->ci->currentUser->id)->first();

        // TO DO - Klaviyo send joined_league event
        $eventData = [
            "data" => [
                "type" => "event",
                "attributes" => [
                    "properties" => [],
                    "metric" => [
                        "data" => [
                            "type" => "metric",
                            "attributes" => [
                                "name" => "Created League"
                            ]
                        ]
                    ],
                    "profile" => [
                        "data" => [
                            "type" => "profile",
                            "id" => $consent->klaviyo_id,
                            "attributes" => [],
                        ]
                    ]
                ]
            ]
        ];
        $klaviyo->Events->createEvent($eventData);

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

                $entry_fee = 0;
                if($params['entry_fee']>0){
                    $entry_fee = $params['entry_fee'];
                }

                // Create new round and set status to 'active'
                $newRound = new Round([
                    'start_gameweek' => $params['start_gameweek'],
                    'league_id' => $league->id,
                    'status' => 'active',
                    'current_gameweek' => $params['start_gameweek'],
                    'entry_fee' => $entry_fee
                ]);
                $newRound->save();
                $klaviyo = new KlaviyoAPI(
                    'pk_79382b5640e6efb5387e0d28362b6ed032', 
                    $num_retries = 3, 
                    $wait_seconds = 3,
                    $guzzle_options = [],
                    $user_agent_suffix = "/FootballKnockout");
                // Create new round_user for each user that played in the previous round
                $roundUsers = RoundUser::where('round_id', $round->id)->get();
                foreach ($roundUsers as $ru) {
                    $newRoundUser = new RoundUser([
                        'user_id' => $ru->user_id,
                        'round_id' => $newRound->id,
                        'status' => 'active'
                    ]);
                    $newRoundUser->save();

                    // klaviyo event for New Round Started
                    $consent = MarketingConsent::where('user_id', $ru->user_id)->first();
                    $eventData = [
                        "data" => [
                            "type" => "event",
                            "attributes" => [
                                "properties" => [],
                                "metric" => [
                                    "data" => [
                                        "type" => "metric",
                                        "attributes" => [
                                            "name" => "New Round Created"
                                        ]
                                    ]
                                ],
                                "profile" => [
                                    "data" => [
                                        "type" => "profile",
                                        "id" => $consent->klaviyo_id,
                                        "attributes" => [],
                                    ]
                                ]
                            ]
                        ]
                    ];
                    if(strlen($consent->klaviyo_id) > 0){
                        $klaviyo->Events->createEvent($eventData);
                    }
                    // End Klaviyo event
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

    public function pageViewPreviousRound(Request $request, Response $response, $args)
    {
        $round = Round::where('round.id', $args['round_id'])->first();
        $ms = $this->ci->alerts;

        //Check Round Exists
        if(!$round){
            $ms->addMessage('warning', "This previous Round cannot be viewed.");

            $leaguesPage = $this->ci->router->pathFor('leagues');
            return $response->withRedirect($leaguesPage);
        }
        //Check Round has ended
        if($round->status != 'ended'){
            $ms->addMessage('warning', "This previous Round cannot be viewed.");

            $leaguesPage = $this->ci->router->pathFor('leagues');
            return $response->withRedirect($leaguesPage);
        }

        $round_users = RoundUser::where('round_id' , $round->id)
            ->leftJoin('users', 'users.id', 'round_user.user_id')
            ->get();

        $picks = Pick::where('round.id', $round->id)
            ->leftJoin('round_user', 'round_user.id', 'pick.round_user_id')
            ->leftJoin('round', 'round.id', 'round_user.round_id')
            ->get();
        
        $league = League::where('id', $round->league_id)->first();
        $teams = Team::get();
        $gameweeks = Gameweek::get();

        return $this->ci->view->render($response, 'pages/pageViewPreviousRound.html.twig', [
            'round' => $round,
            'round_users' => $round_users,
            'league' => $league,
            'teams' => $teams,
            'gameweeks' => $gameweeks,
            'picks' => $picks
        ]);
    }
}

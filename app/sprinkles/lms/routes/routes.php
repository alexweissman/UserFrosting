<?php


/*
 * Routes for League management.
 */
$app->group('/leagues', function () {
    $this->get('', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageLeagues')
        ->setName('leagues');

    $this->get('/create-league', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageCreateLeague');
    $this->post('/create-league', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:createLeague');
    $this->get('/join-league', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageJoinLeague');
    $this->get('/join/{league_id/{joining_code}', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:joinLeagueByLink');
    $this->post('/join-league', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:joinLeague');
    $this->get('/view-league/{league_id}', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageViewLeague');
    $this->get('/{league_id}/new-round', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageCreateNewRound');
    $this->post('/new-round', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:createNewRound');
})->add('authGuard');

$app->group('/pick', function () {
    $this->get('/{league_id}', 'UserFrosting\Sprinkle\Lms\Controller\PickController:pageMakePick')
        ->setName('leagues');
    $this->post('/new/{league_id}', 'UserFrosting\Sprinkle\Lms\Controller\PickController:makeNewPick');
})->add('authGuard');

$app->group('/gameweek', function () {
    $this->get('', 'UserFrosting\Sprinkle\Lms\Controller\GameweekController:pageRollOnGameweek')
        ->setName('gameweeks');
    $this->get('/{gameweek_id}', 'UserFrosting\Sprinkle\Lms\Controller\GameweekController:PageRollOnGameweekChosen');
    $this->get('/{gameweek_id}/process', 'UserFrosting\Sprinkle\Lms\Controller\GameweekController:RollOnGameweek');
})->add('authGuard');

$app->group('/fixture', function () {
    $this->post('/result', 'UserFrosting\Sprinkle\Lms\Controller\FixtureController:UpdateResult');
})->add('authGuard');

$app->group('/emails', function () {
    $this->get('/pick-reminder/{gameweek_id}', 'UserFrosting\Sprinkle\Lms\Controller\EmailController:pickReminder');
})->add('authGuard');


$app->get('/', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageIndex')
    ->add('checkEnvironment')
    ->setName('index');
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
    $this->post('/join-league', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:joinLeague');
    $this->get('/view-league/{league_id}', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageViewLeague');
    $this->get('/{league_id}/new-round', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageCreateNewRound');
    $this->post('/new-round', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:createNewRound');
    $this->post('/round/mark-paid', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:markPaidEntryFee');
    $this->get('/{league_id}/previous-round/{round_id}', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageViewPreviousRound');
})->add('authGuard');

$app->get('/auto-join/{joining_code}', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:joinLeagueByLink');

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

$app->group('/manage', function () {
    $this->get('/leagues', 'UserFrosting\Sprinkle\Lms\Controller\ManageController:PageManageLeagues');
    $this->get('/leagues/{league_id}', 'UserFrosting\Sprinkle\Lms\Controller\ManageController:PageManageLeague');
})->add('authGuard');

$app->group('/fixture', function () {
    $this->post('/result', 'UserFrosting\Sprinkle\Lms\Controller\FixtureController:UpdateResult');
})->add('authGuard');

$app->group('/blog', function () {
    $this->get('', 'UserFrosting\Sprinkle\Lms\Controller\BlogController:pageBlogIndex')
        ->setName('blog');
    $this->get('/{blog_slug}', 'UserFrosting\Sprinkle\Lms\Controller\BlogController:pageBlogArticle');
});

$app->group('/rules', function () {
    $this->get('', 'UserFrosting\Sprinkle\Lms\Controller\BlogController:pageRules')
        ->setName('blog');
});

$app->group('/emails', function () {
    $this->get('/pick-reminder/{gameweek_id}', 'UserFrosting\Sprinkle\Lms\Controller\EmailController:pickReminder');
})->add('authGuard');

$app->post('/account/marketing', 'UserFrosting\Sprinkle\Account\Controller\AccountController:marketing')   
    ->add('authGuard');

$app->get('/', 'UserFrosting\Sprinkle\Lms\Controller\LeagueController:pageIndex')
    ->add('checkEnvironment')
    ->setName('index');

$app->group('/webhooks', function () {
    $this->post('/receive', 'UserFrosting\Sprinkle\Lms\Controller\WebhookController:ReceiveWebhook');
});
<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2013-2024 Alexander Weissman & Louis Charette
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

/**
 * AppController Class.
 *
 * Implements some common site wide routes.
 *
 * N.B.: This file is sage to edit or delete. If you delete this class, don't
 *       forget to delete the corresponding routes and entry in the Sprinkle Recipe!
 */
class AppController
{
    /**
     * Renders the default home page for UserFrosting.
     * By default, this is the page that non-authenticated users will first see when they navigate to your website's root.
     * Request type: GET.
     *
     * @param Response $response
     * @param Twig     $view
     */
    public function pageIndex(Response $response, Twig $view): Response
    {
        return $view->render($response, 'pages/spa.html.twig');
    }
}

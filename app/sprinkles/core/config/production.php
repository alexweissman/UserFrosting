<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

/*
 * Default production config file for UserFrosting.  You may override/extend this in your site's configuration file to customize deploy settings.
 */
return [
    /*
     * Use compiled assets
     */
    'assets' => [
        'use_raw' => false,
    ],
    /*
     * Enable Twig cache
     */
    'cache' => [
        'twig' => true,
    ],
    /*
     * Turn off debug logs
     */
    'debug' => [
        'twig' => false,
        'auth' => false,
        'smtp' => false,
    ],
    /*
     * Use router cache, disable full error details
     */
    'settings' => [
        'routerCacheFile'     => 'routes.cache',
        'displayErrorDetails' => false,
    ],
    /*
     * Enable analytics, disable more debugging
     */
    'site' => [
        'analytics' => [
            'google' => [
                'enabled' => true,
            ],
        ],
        'debug' => [
            'ajax' => false,
            'info' => false,
        ],
        'uri' => [
            'public' => 'https://example.com',
        ],
    ],
    /*
     * Send errors to log
     */
    'php' => [
        'display_errors'  => 'false',
        'log_errors'      => 'true',
    ],

    'csrf' => [
        'enabled'          => env('CSRF_ENABLED', true),
        'name'             => 'csrf',
        'storage_limit'    => 200,
        'strength'         => 16,
        'persistent_token' => true,
        'blacklist'        => [
            // A list of url paths to ignore CSRF checks on
            // URL paths will be matched against each regular expression in this list.
            // Each regular expression should map to an array of methods.
            // Regular expressions will be delimited with ~ in preg_match, so if you
            // have routes with ~ in them, you must escape this character in your regex.
            // Also, remember to use ^ when you only want to match the beginning of a URL path!
            '/webhooks/receive' => [
                'POST'
            ],
        ],
    ],
];

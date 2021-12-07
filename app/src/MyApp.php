<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\App;

use Fullpipe\TwigWebpackExtension\WebpackExtension;
use UserFrosting\App\Bakery\HelloCommand;
// use UserFrosting\Sprinkle\Account\Account;
// use UserFrosting\Sprinkle\Admin\Admin;
use UserFrosting\Sprinkle\Core\Core;
use UserFrosting\Sprinkle\Core\Sprinkle\Recipe\LocatorRecipe;
use UserFrosting\Sprinkle\Core\Sprinkle\Recipe\TwigExtensionRecipe;
use UserFrosting\Sprinkle\SprinkleRecipe;
use UserFrosting\UniformResourceLocator\ResourceStream;

class MyApp implements SprinkleRecipe, TwigExtensionRecipe, LocatorRecipe
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'My Application';
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return __DIR__ . '/../';
    }

    /**
     * {@inheritdoc}
     */
    public function getBakeryCommands(): array
    {
        return [
            HelloCommand::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSprinkles(): array
    {
        return [
            Core::class,
            // Account::class,
            // Admin::class,
        ];
    }

    /**
     * Returns a list of routes definition in PHP files.
     *
     * @return string[]
     */
    public function getRoutes(): array
    {
        return [
            Routes::class,
        ];
    }

    /**
     * Returns a list of all PHP-DI services/container definitions files.
     *
     * @return string[]
     */
    public function getServices(): array
    {
        return [
            Services::class,
        ];
    }

    /**
     * Returns a list of all Middlewares classes.
     *
     * @return \Psr\Http\Server\MiddlewareInterface[]
     */
    public function getMiddlewares(): array
    {
        return [];
    }

    public function getTwigExtensions(): array
    {
        return [
            WebpackExtension::class,
        ];
    }

    /**
     * Return an array of all locator Resource Steams to register with locator.
     *
     * @return \UserFrosting\UniformResourceLocator\ResourceStreamInterface[]
     */
    public function getResourceStreams(): array
    {
        return [
            new ResourceStream('public', path: self::getPath() . '../public', shared: true),
        ];
    }
}

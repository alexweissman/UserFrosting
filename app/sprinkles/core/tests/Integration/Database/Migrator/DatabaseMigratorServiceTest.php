<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Core\Tests\Integration\Database\Migrator;

use UserFrosting\Sprinkle\Core\Database\Migrator\Migrator;
use UserFrosting\Sprinkle\Core\Tests\TestDatabase;
use UserFrosting\Tests\TestCase;

/**
 * Tests for the Migrator Service
 *
 * @author Louis Charette
 */
class DatabaseMigratorServiceTest extends TestCase
{
    use TestDatabase;

    public function testMigratorService()
    {
        $this->setupTestDatabase();

        $this->assertInstanceOf(Migrator::class, $this->ci->migrator);
    }
}

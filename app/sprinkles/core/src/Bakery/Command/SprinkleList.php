<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Core\Bakery\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserFrosting\Sprinkle\Core\Bakery\AbstractCommand;

/**
 * Sprinkle:list CLI tool.
 */
class SprinkleList extends AbstractCommand
{
    /**
     * @var array The table header
     */
    protected $headers = ['Sprinkle', 'Calculated Namespace', 'Calculated Path'];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sprinkle:list')
             ->setDescription('List all available sprinkles and their params');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Loaded Sprinkles');

        /** @var \UserFrosting\Sprinkle\Core\Sprinkle\SprinkleManager $sprinkleManager */
        $sprinkleManager = $this->ci->sprinkleManager;

        // Get sprinkle list
        $sprinkles = $sprinkleManager->getSprinkleNames();

        // Compile the routes into a displayable format
        $sprinklesTable = collect($sprinkles)->map(function ($sprinkle) use ($sprinkleManager) {
            return [
                'sprinkle'  => $sprinkle,
                'namespace' => $sprinkleManager->getSprinkleClassNamespace($sprinkle),
                'path'      => $sprinkleManager->getSprinklePath($sprinkle),
            ];
        })->all();

        // Display table
        $this->io->table($this->headers, $sprinklesTable);
    }
}

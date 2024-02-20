<?php

namespace UserFrosting\Sprinkle\Lms\Database\Seeds;

use UserFrosting\Sprinkle\Lms\Database\Models\Gameweek;
use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;

/**
 * Seeder for the default roles.
 */
class DefaultGameweeks extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $gameweeks = $this->getGameweeks();

        foreach ($gameweeks as $gameweek) {
            // Don't save if already exist
            if (Gameweek::where('gameweek_number', $gameweek->gameweek_number)->first() == null) {
                $gameweek->save();
            }
        }
    }

    /**
     * @return array Teams to seed
     */
    protected function getGameweeks()
    {
        return [
            new Gameweek([
                'gameweek_number'        => 1,
                'deadline' => '2022-07-25 15:00:00',
                'status' => 'complete'
            ]),
            new Gameweek([
                'gameweek_number'        => 2,
                'deadline' => '2022-08-01 15:00:00'
            ]),
            new Gameweek([
                'gameweek_number'        => 3,
                'deadline' => '2022-08-08 15:00:00'
            ]),
        ];
    }
}

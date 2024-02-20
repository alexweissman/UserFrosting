<?php

namespace UserFrosting\Sprinkle\Lms\Database\Seeds;

use UserFrosting\Sprinkle\Lms\Database\Models\Fixture;
use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;

/**
 * Seeder for the default roles.
 */
class DefaultFixtures extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $fixtures = $this->getFixtures();

        foreach ($fixtures as $fixture) {
            // Don't save if already exist
            if (Fixture::where('gameweek_id', $gameweek->gameweek_number)->first() == null) {
                $fixture->save();
            }
        }
    }

    /**
     * @return array Teams to seed
     */
    protected function getFixtures()
    {
        return [
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 7,
                'away_team' => 1,
                'home_difficulty' => 2,
                'away_difficulty'=> 3,
                'result' => 1
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 10,
                'away_team' => 13,
                'home_difficulty' => 5,
                'away_difficulty'=> 2,
                'result' => 13
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 3,
                'away_team' => 2,
                'home_difficulty' => 2,
                'away_difficulty'=> 2,
                'result' => 3
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 11,
                'away_team' => 20,                
                'home_difficulty' => 2,
                'away_difficulty'=> 2,
                'result' => null
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 16,
                'away_team' => 9,
                'home_difficulty' => 2,
                'away_difficulty'=> 3
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 18,
                'away_team' => 17,                
                'home_difficulty' => 2,
                'away_difficulty'=> 4
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 8,
                'away_team' => 6,                
                'home_difficulty' => 4,
                'away_difficulty'=> 2
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 12,
                'away_team' => 4,                
                'home_difficulty' => 2,
                'away_difficulty'=> 4
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 15,
                'away_team' => 5,
                'home_difficulty' => 2,
                'away_difficulty'=> 4
            ]),
            new Fixture([
                'gameweek_id' => 1,
                'home_team' => 19,
                'away_team' => 14,
                'home_difficulty' => 5,
                'away_difficulty'=> 3
            ]),
            



            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 7,
                'away_team' => 1,
                'home_difficulty' => 2,
                'away_difficulty'=> 3
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 10,
                'away_team' => 13,
                'home_difficulty' => 5,
                'away_difficulty'=> 2
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 3,
                'away_team' => 2,
                'home_difficulty' => 2,
                'away_difficulty'=> 2
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 11,
                'away_team' => 20,                
                'home_difficulty' => 2,
                'away_difficulty'=> 2
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 16,
                'away_team' => 9,
                'home_difficulty' => 2,
                'away_difficulty'=> 3
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 18,
                'away_team' => 17,                
                'home_difficulty' => 2,
                'away_difficulty'=> 4
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 8,
                'away_team' => 6,                
                'home_difficulty' => 4,
                'away_difficulty'=> 2
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 12,
                'away_team' => 4,                
                'home_difficulty' => 2,
                'away_difficulty'=> 4
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 15,
                'away_team' => 5,
                'home_difficulty' => 2,
                'away_difficulty'=> 4
            ]),
            new Fixture([
                'gameweek_id' => 2,
                'home_team' => 19,
                'away_team' => 14,
                'home_difficulty' => 5,
                'away_difficulty'=> 3
            ]),
        ];
    }
}
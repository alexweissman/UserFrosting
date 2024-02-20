<?php

namespace UserFrosting\Sprinkle\Lms\Database\Seeds;

use UserFrosting\Sprinkle\Lms\Database\Models\Team;
use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;

/**
 * Seeder for the default roles.
 */
class DefaultTeams extends BaseSeed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $teams = $this->getTeams();

        foreach ($teams as $team) {
            // Don't save if already exist
            if (Team::where('team_name', $team->team_name)->first() == null) {
                $team->save();
            }
        }
    }

    /**
     * @return array Teams to seed
     */
    protected function getTeams()
    {
        return [
            new Team([
                'id' => 1,
                'team_name'        => 'Arsenal',
                'image_name'        => 'arsenal.png',
                'team_name_shortened' => 'ARS'
            ]),
            new Team([
                'id' => 2,
                'team_name'        => 'Aston Villa',
                'image_name'        => 'aston-villa.png',
                'team_name_shortened' => 'AVL'
            ]),
            new Team([
                'id' => 3,
                'team_name'        => 'Bournemouth',
                'image_name'        => 'bournemouth.png',
                'team_name_shortened' => 'BOU'
            ]),
            new Team([
                'id' => 4,
                'team_name'        => 'Brentford',
                'image_name'        => 'brentford.png',
                'team_name_shortened' => 'BRE'
            ]),
            new Team([
                'id' => 5,
                'team_name'        => 'Brighton',
                'image_name'        => 'brighton.png',
                'team_name_shortened' => 'BRI'
            ]),
            new Team([
                'id' => 6,
                'team_name'        => 'Chelsea',
                'image_name'        => 'chelsea.png',
                'team_name_shortened' => 'CHE'
            ]),
            new Team([
                'id' => 7,
                'team_name'        => 'Crystal Palace',
                'image_name'        => 'crystal-palace.png',
                'team_name_shortened' => 'CPL'
            ]),
            new Team([
                'id' => 8,
                'team_name'        => 'Everton',
                'image_name'        => 'everton.png',
                'team_name_shortened' => 'EVE'
            ]),
            new Team([
                'id' => 9,
                'team_name'        => 'Forest',
                'image_name'        => 'forest.png',
                'team_name_shortened' => 'FOR'
            ]),
            new Team([
                'id' => 10,
                'team_name'        => 'Fulham',
                'image_name'        => 'fulham.png',
                'team_name_shortened' => 'FUL'
            ]),
            new Team([
                'id' => 11,
                'team_name'        => 'Leeds',
                'image_name'        => 'leeds.png',
                'team_name_shortened' => 'LEE'
            ]),
            new Team([
                'id' => 12,
                'team_name'        => 'Leicester',
                'image_name'        => 'leicester.png',
                'team_name_shortened' => 'LEI'
            ]),
            new Team([
                'id' => 13,
                'team_name'        => 'Liverpool',
                'image_name'        => 'liverpool.png',
                'team_name_shortened' => 'LIV'
            ]),
            new Team([
                'id' => 14,
                'team_name'        => 'Man City',
                'image_name'        => 'man-city.png',
                'team_name_shortened' => 'MCI'
            ]),
            new Team([
                'id' => 15,
                'team_name'        => 'Man United',
                'image_name'        => 'man-utd.png',
                'team_name_shortened' => 'MUN'
            ]),
            new Team([
                'id' => 16,
                'team_name'        => 'Newcastle',
                'image_name'        => 'newcastle.png',
                'team_name_shortened' => 'NEW'
            ]),
            new Team([
                'id' => 17,
                'team_name'        => 'Southampton',
                'image_name'        => 'southampton.png',
                'team_name_shortened' => 'SOT'
            ]),
            new Team([
                'id' => 18,
                'team_name'        => 'Tottenham',
                'image_name'        => 'tottenham.png',
                'team_name_shortened' => 'TOT'
            ]),
            new Team([
                'id' => 19,
                'team_name'        => 'West Ham',
                'image_name'        => 'west-ham.png',
                'team_name_shortened' => 'WHU'
            ]),
            new Team([
                'id' => 20,
                'team_name'        => 'Wolves',
                'image_name'        => 'wolves.png',
                'team_name_shortened' => 'WOL'
            ]),

        ];
    }
}

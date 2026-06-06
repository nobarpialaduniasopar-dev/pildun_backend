<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Standing;

class StandingSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'GROUP A' => [
                ['team_name' => 'INDONESIA', 'flag_url' => 'https://flagcdn.com/id.svg', 'points' => 9, 'won' => 3],
                ['team_name' => 'ARGENTINA', 'flag_url' => 'https://flagcdn.com/ar.svg', 'points' => 6, 'won' => 2],
                ['team_name' => 'JAPAN', 'flag_url' => 'https://flagcdn.com/jp.svg', 'points' => 3, 'won' => 1],
                ['team_name' => 'GERMANY', 'flag_url' => 'https://flagcdn.com/de.svg', 'points' => 0, 'won' => 0],
            ],
            'GROUP B' => [
                ['team_name' => 'BRAZIL', 'flag_url' => 'https://flagcdn.com/br.svg', 'points' => 7, 'won' => 2],
                ['team_name' => 'FRANCE', 'flag_url' => 'https://flagcdn.com/fr.svg', 'points' => 5, 'won' => 1],
                ['team_name' => 'ENGLAND', 'flag_url' => 'https://flagcdn.com/gb-eng.svg', 'points' => 4, 'won' => 1],
                ['team_name' => 'SPAIN', 'flag_url' => 'https://flagcdn.com/es.svg', 'points' => 0, 'won' => 0],
            ]
        ];

        foreach ($groups as $groupName => $teams) {
            foreach ($teams as $team) {
                Standing::create([
                    'group_name' => $groupName,
                    'team_name' => $team['team_name'],
                    'flag_url' => $team['flag_url'],
                    'played' => 3,
                    'won' => $team['won'],
                    'drawn' => $team['points'] == 7 || $team['points'] == 5 || $team['points'] == 4 ? 1 : 0,
                    'lost' => 3 - $team['won'] - ($team['points'] % 3 == 0 ? 0 : 1),
                    'points' => $team['points'],
                    'goals_for' => rand(3, 8),
                    'goals_against' => rand(1, 5),
                ]);
            }
        }
    }
}
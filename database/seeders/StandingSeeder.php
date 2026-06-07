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
                ['name' => 'Meksiko', 'flag' => 'https://flagcdn.com/mx.svg'],
                ['name' => 'Afrika Selatan', 'flag' => 'https://flagcdn.com/za.svg'],
                ['name' => 'Korea Selatan', 'flag' => 'https://flagcdn.com/kr.svg'],
                ['name' => 'Republik Ceko', 'flag' => 'https://flagcdn.com/cz.svg'],
            ],
            'GROUP B' => [
                ['name' => 'Kanada', 'flag' => 'https://flagcdn.com/ca.svg'],
                ['name' => 'Bosnia dan Herzegovina', 'flag' => 'https://flagcdn.com/ba.svg'],
                ['name' => 'Qatar', 'flag' => 'https://flagcdn.com/qa.svg'],
                ['name' => 'Swiss', 'flag' => 'https://flagcdn.com/ch.svg'],
            ],
            'GROUP C' => [
                ['name' => 'Brasil', 'flag' => 'https://flagcdn.com/br.svg'],
                ['name' => 'Maroko', 'flag' => 'https://flagcdn.com/ma.svg'],
                ['name' => 'Haiti', 'flag' => 'https://flagcdn.com/ht.svg'],
                ['name' => 'Skotlandia', 'flag' => 'https://flagcdn.com/gb-sct.svg'],
            ],
            'GROUP D' => [
                ['name' => 'Amerika Serikat', 'flag' => 'https://flagcdn.com/us.svg'],
                ['name' => 'Paraguay', 'flag' => 'https://flagcdn.com/py.svg'],
                ['name' => 'Australia', 'flag' => 'https://flagcdn.com/au.svg'],
                ['name' => 'Turki', 'flag' => 'https://flagcdn.com/tr.svg'],
            ],
            'GROUP E' => [
                ['name' => 'Jerman', 'flag' => 'https://flagcdn.com/de.svg'],
                ['name' => 'Curacao', 'flag' => 'https://flagcdn.com/cw.svg'],
                ['name' => 'Pantai Gading', 'flag' => 'https://flagcdn.com/ci.svg'],
                ['name' => 'Ekuador', 'flag' => 'https://flagcdn.com/ec.svg'],
            ],
            'GROUP F' => [
                ['name' => 'Belanda', 'flag' => 'https://flagcdn.com/nl.svg'],
                ['name' => 'Jepang', 'flag' => 'https://flagcdn.com/jp.svg'],
                ['name' => 'Swedia', 'flag' => 'https://flagcdn.com/se.svg'],
                ['name' => 'Tunisia', 'flag' => 'https://flagcdn.com/tn.svg'],
            ],
            'GROUP G' => [
                ['name' => 'Belgia', 'flag' => 'https://flagcdn.com/be.svg'],
                ['name' => 'Mesir', 'flag' => 'https://flagcdn.com/eg.svg'],
                ['name' => 'Iran', 'flag' => 'https://flagcdn.com/ir.svg'],
                ['name' => 'Selandia Baru', 'flag' => 'https://flagcdn.com/nz.svg'],
            ],
            'GROUP H' => [
                ['name' => 'Spanyol', 'flag' => 'https://flagcdn.com/es.svg'],
                ['name' => 'Tanjung Verde', 'flag' => 'https://flagcdn.com/cv.svg'],
                ['name' => 'Arab Saudi', 'flag' => 'https://flagcdn.com/sa.svg'],
                ['name' => 'Uruguay', 'flag' => 'https://flagcdn.com/uy.svg'],
            ],
            'GROUP I' => [
                ['name' => 'Prancis', 'flag' => 'https://flagcdn.com/fr.svg'],
                ['name' => 'Senegal', 'flag' => 'https://flagcdn.com/sn.svg'],
                ['name' => 'Irak', 'flag' => 'https://flagcdn.com/iq.svg'],
                ['name' => 'Norwegia', 'flag' => 'https://flagcdn.com/no.svg'],
            ],
            'GROUP J' => [
                ['name' => 'Argentina', 'flag' => 'https://flagcdn.com/ar.svg'],
                ['name' => 'Aljazair', 'flag' => 'https://flagcdn.com/dz.svg'],
                ['name' => 'Austria', 'flag' => 'https://flagcdn.com/at.svg'],
                ['name' => 'Yordania', 'flag' => 'https://flagcdn.com/jo.svg'],
            ],
            'GROUP K' => [
                ['name' => 'Portugal', 'flag' => 'https://flagcdn.com/pt.svg'],
                ['name' => 'RD Kongo', 'flag' => 'https://flagcdn.com/cd.svg'],
                ['name' => 'Uzbekistan', 'flag' => 'https://flagcdn.com/uz.svg'],
                ['name' => 'Kolombia', 'flag' => 'https://flagcdn.com/co.svg'],
            ],
            'GROUP L' => [
                ['name' => 'Inggris', 'flag' => 'https://flagcdn.com/gb-eng.svg'],
                ['name' => 'Kroasia', 'flag' => 'https://flagcdn.com/hr.svg'],
                ['name' => 'Ghana', 'flag' => 'https://flagcdn.com/gh.svg'],
                ['name' => 'Panama', 'flag' => 'https://flagcdn.com/pa.svg'],
            ]
        ];

        Standing::truncate();

        foreach ($groups as $groupName => $teams) {
            $pointsArr = [7, 5, 4, 0];
            foreach ($teams as $index => $team) {
                $points = $pointsArr[$index];
                Standing::create([
                    'group_name' => $groupName,
                    'team_name' => strtoupper($team['name']),
                    'flag_url' => $team['flag'],
                    'played' => 0,
                    'won' => 0,
                    'drawn' => 0,
                    'lost' => 0,
                    'points' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                ]);
            }
        }
    }
}
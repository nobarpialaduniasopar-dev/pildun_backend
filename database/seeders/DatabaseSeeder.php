<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MatchSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Akun Admin Mutlak (Sesuai PRD)
        User::updateOrCreate(
            ['email' => 'admin@nobar.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin'), 
            ]
        );

        // 2. Setup Data Dummy Hot Match untuk Testing UI
        MatchSchedule::create([
            'team_a' => 'Argentina',
            'team_b' => 'Prancis',
            'flag_a_url' => 'https://flagcdn.com/ar.svg',
            'flag_b_url' => 'https://flagcdn.com/fr.svg',
            'match_date' => '2026-07-19 19:00:00', // Pastikan format waktu ini masuk sebagai WIB
            'venue' => 'Solo Paragon',
            'price' => 150000,
            'quota' => 500,
            'is_hot_match' => true,
        ]);
        
        // 3. Setup Data Dummy Upcoming Match
        MatchSchedule::create([
            'team_a' => 'Inggris',
            'team_b' => 'Brasil',
            'flag_a_url' => 'https://flagcdn.com/gb-eng.svg',
            'flag_b_url' => 'https://flagcdn.com/br.svg',
            'match_date' => now()->addDays(2)->format('Y-m-d H:i:s'), // H-2 untuk masuk filter Upcoming
            'venue' => 'Solo Paragon',
            'price' => 100000,
            'quota' => 300,
            'is_hot_match' => false,
        ]);

        $this->call([
            StandingSeeder::class,
        ]);
    }
}
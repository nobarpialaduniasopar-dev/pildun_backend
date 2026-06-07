<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MatchSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Map nama negara ke kode flagcdn
     */
    private function getFlagUrl($team)
    {
        $flags = [
            'Afrika Selatan' => 'za', 'Aljazair' => 'dz', 'Arab Saudi' => 'sa', 'Argentina' => 'ar',
            'Amerika Serikat' => 'us', 'AS' => 'us', 'Australia' => 'au', 'Austria' => 'at',
            'Belanda' => 'nl', 'Belgia' => 'be', 'Bosnia & Herzegovina' => 'ba', 'Brasil' => 'br',
            'Ceko' => 'cz', 'Curacao' => 'cw', 'Ekuador' => 'ec', 'Ghana' => 'gh', 'Haiti' => 'ht',
            'Inggris' => 'gb-eng', 'Iran' => 'ir', 'Irak' => 'iq', 'Jepang' => 'jp', 'Jerman' => 'de',
            'Kanada' => 'ca', 'Kolombia' => 'co', 'Korea Selatan' => 'kr', 'Kroasia' => 'hr',
            'Maroko' => 'ma', 'Meksiko' => 'mx', 'Mesir' => 'eg', 'Norwegia' => 'no', 'Panama' => 'pa',
            'Pantai Gading' => 'ci', 'Paraguay' => 'py', 'Portugal' => 'pt', 'Prancis' => 'fr',
            'Qatar' => 'qa', 'RD Kongo' => 'cd', 'Selandia Baru' => 'nz', 'Senegal' => 'sn',
            'Skotlandia' => 'gb-sct', 'Spanyol' => 'es', 'Swedia' => 'se', 'Swiss' => 'ch',
            'Tanjung Verde' => 'cv', 'Tunisia' => 'tn', 'Turki' => 'tr', 'Uruguay' => 'uy',
            'Uzbekistan' => 'uz', 'Yordania' => 'jo'
        ];

        $code = $flags[$team] ?? 'xx';
        return "https://flagcdn.com/w80/{$code}.png"; // Menggunakan PNG W80 agar senada dengan frontend
    }

    /**
     * Cek apakah tim termasuk dalam kategori Hot Match
     */
    private function isHotMatch($teamA, $teamB)
    {
        $bigTeams = ['Argentina', 'Brasil', 'Inggris', 'Jerman', 'Prancis', 'Spanyol', 'Portugal', 'Belanda', 'Italia'];
        return in_array($teamA, $bigTeams) || in_array($teamB, $bigTeams);
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Akun Admin Mutlak
        User::updateOrCreate(
            ['email' => 'admin@nobar.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // Sesuai password yang kita buat sebelumnya
            ]
        );

        // 2. Data Master Pertandingan Fase Grup Piala Dunia 2026
        $matches = [
            // 11 Juni 2026
            ['date' => '2026-06-11 19:00:00', 'a' => 'Meksiko', 'b' => 'Afrika Selatan'],
            ['date' => '2026-06-11 22:00:00', 'a' => 'Korea Selatan', 'b' => 'Ceko'],
            // 12 Juni 2026
            ['date' => '2026-06-12 19:00:00', 'a' => 'Kanada', 'b' => 'Bosnia & Herzegovina'],
            ['date' => '2026-06-12 22:00:00', 'a' => 'Amerika Serikat', 'b' => 'Paraguay'],
            // 13 Juni 2026
            ['date' => '2026-06-13 16:00:00', 'a' => 'Haiti', 'b' => 'Skotlandia'],
            ['date' => '2026-06-13 19:00:00', 'a' => 'Australia', 'b' => 'Turki'],
            ['date' => '2026-06-13 22:00:00', 'a' => 'Brasil', 'b' => 'Maroko'],
            ['date' => '2026-06-13 02:00:00', 'a' => 'Qatar', 'b' => 'Swiss'],
            // 14 Juni 2026
            ['date' => '2026-06-14 16:00:00', 'a' => 'Pantai Gading', 'b' => 'Ekuador'],
            ['date' => '2026-06-14 19:00:00', 'a' => 'Jerman', 'b' => 'Curacao'],
            ['date' => '2026-06-14 22:00:00', 'a' => 'Belanda', 'b' => 'Jepang'],
            ['date' => '2026-06-14 02:00:00', 'a' => 'Swedia', 'b' => 'Tunisia'],
            // 15 Juni 2026
            ['date' => '2026-06-15 16:00:00', 'a' => 'Arab Saudi', 'b' => 'Uruguay'],
            ['date' => '2026-06-15 19:00:00', 'a' => 'Spanyol', 'b' => 'Tanjung Verde'],
            ['date' => '2026-06-15 22:00:00', 'a' => 'Iran', 'b' => 'Selandia Baru'],
            ['date' => '2026-06-15 02:00:00', 'a' => 'Belgia', 'b' => 'Mesir'],
            // 16 Juni 2026
            ['date' => '2026-06-16 16:00:00', 'a' => 'Prancis', 'b' => 'Senegal'],
            ['date' => '2026-06-16 19:00:00', 'a' => 'Irak', 'b' => 'Norwegia'],
            ['date' => '2026-06-16 22:00:00', 'a' => 'Argentina', 'b' => 'Aljazair'],
            ['date' => '2026-06-16 02:00:00', 'a' => 'Austria', 'b' => 'Yordania'],
            // 17 Juni 2026
            ['date' => '2026-06-17 16:00:00', 'a' => 'Ghana', 'b' => 'Panama'],
            ['date' => '2026-06-17 19:00:00', 'a' => 'Inggris', 'b' => 'Kroasia'],
            ['date' => '2026-06-17 22:00:00', 'a' => 'Portugal', 'b' => 'RD Kongo'],
            ['date' => '2026-06-17 02:00:00', 'a' => 'Uzbekistan', 'b' => 'Kolombia'],
            // 18 Juni 2026
            ['date' => '2026-06-18 16:00:00', 'a' => 'Ceko', 'b' => 'Afrika Selatan'],
            ['date' => '2026-06-18 19:00:00', 'a' => 'Swiss', 'b' => 'Bosnia & Herzegovina'],
            ['date' => '2026-06-18 22:00:00', 'a' => 'Kanada', 'b' => 'Qatar'],
            ['date' => '2026-06-18 02:00:00', 'a' => 'Meksiko', 'b' => 'Korea Selatan'],
            // 19 Juni 2026
            ['date' => '2026-06-19 16:00:00', 'a' => 'Brasil', 'b' => 'Haiti'],
            ['date' => '2026-06-19 19:00:00', 'a' => 'Skotlandia', 'b' => 'Maroko'],
            ['date' => '2026-06-19 22:00:00', 'a' => 'Turki', 'b' => 'Paraguay'],
            ['date' => '2026-06-19 02:00:00', 'a' => 'Amerika Serikat', 'b' => 'Australia'],
            // 20 Juni 2026
            ['date' => '2026-06-20 16:00:00', 'a' => 'Jerman', 'b' => 'Pantai Gading'],
            ['date' => '2026-06-20 19:00:00', 'a' => 'Ekuador', 'b' => 'Curacao'],
            ['date' => '2026-06-20 22:00:00', 'a' => 'Belanda', 'b' => 'Swedia'],
            ['date' => '2026-06-20 02:00:00', 'a' => 'Tunisia', 'b' => 'Jepang'],
            // 21 Juni 2026
            ['date' => '2026-06-21 16:00:00', 'a' => 'Uruguay', 'b' => 'Tanjung Verde'],
            ['date' => '2026-06-21 19:00:00', 'a' => 'Spanyol', 'b' => 'Arab Saudi'],
            ['date' => '2026-06-21 22:00:00', 'a' => 'Belgia', 'b' => 'Iran'],
            ['date' => '2026-06-21 02:00:00', 'a' => 'Selandia Baru', 'b' => 'Mesir'],
            // 22 Juni 2026
            ['date' => '2026-06-22 16:00:00', 'a' => 'Norwegia', 'b' => 'Senegal'],
            ['date' => '2026-06-22 19:00:00', 'a' => 'Prancis', 'b' => 'Irak'],
            ['date' => '2026-06-22 22:00:00', 'a' => 'Argentina', 'b' => 'Austria'],
            ['date' => '2026-06-22 02:00:00', 'a' => 'Yordania', 'b' => 'Aljazair'],
            // 23 Juni 2026
            ['date' => '2026-06-23 16:00:00', 'a' => 'Inggris', 'b' => 'Ghana'],
            ['date' => '2026-06-23 19:00:00', 'a' => 'Panama', 'b' => 'Kroasia'],
            ['date' => '2026-06-23 22:00:00', 'a' => 'Portugal', 'b' => 'Uzbekistan'],
            ['date' => '2026-06-23 02:00:00', 'a' => 'Kolombia', 'b' => 'RD Kongo'],
            // 24 Juni 2026
            ['date' => '2026-06-24 13:00:00', 'a' => 'Skotlandia', 'b' => 'Brasil'],
            ['date' => '2026-06-24 16:00:00', 'a' => 'Maroko', 'b' => 'Haiti'],
            ['date' => '2026-06-24 19:00:00', 'a' => 'Swiss', 'b' => 'Kanada'],
            ['date' => '2026-06-24 22:00:00', 'a' => 'Bosnia & Herzegovina', 'b' => 'Qatar'],
            ['date' => '2026-06-24 01:00:00', 'a' => 'Ceko', 'b' => 'Meksiko'],
            ['date' => '2026-06-24 04:00:00', 'a' => 'Afrika Selatan', 'b' => 'Korea Selatan'],
            // 25 Juni 2026
            ['date' => '2026-06-25 13:00:00', 'a' => 'Curacao', 'b' => 'Pantai Gading'],
            ['date' => '2026-06-25 16:00:00', 'a' => 'Ekuador', 'b' => 'Jerman'],
            ['date' => '2026-06-25 19:00:00', 'a' => 'Jepang', 'b' => 'Swedia'],
            ['date' => '2026-06-25 22:00:00', 'a' => 'Tunisia', 'b' => 'Belanda'],
            ['date' => '2026-06-25 01:00:00', 'a' => 'Turki', 'b' => 'Amerika Serikat'],
            ['date' => '2026-06-25 04:00:00', 'a' => 'Paraguay', 'b' => 'Australia'],
            // 26 Juni 2026
            ['date' => '2026-06-26 13:00:00', 'a' => 'Norwegia', 'b' => 'Prancis'],
            ['date' => '2026-06-26 16:00:00', 'a' => 'Senegal', 'b' => 'Irak'],
            ['date' => '2026-06-26 19:00:00', 'a' => 'Mesir', 'b' => 'Iran'],
            ['date' => '2026-06-26 22:00:00', 'a' => 'Selandia Baru', 'b' => 'Belgia'],
            ['date' => '2026-06-26 01:00:00', 'a' => 'Tanjung Verde', 'b' => 'Arab Saudi'],
            ['date' => '2026-06-26 04:00:00', 'a' => 'Uruguay', 'b' => 'Spanyol'],
            // 27 Juni 2026
            ['date' => '2026-06-27 16:00:00', 'a' => 'Panama', 'b' => 'Inggris'],
            ['date' => '2026-06-27 19:00:00', 'a' => 'Kroasia', 'b' => 'Ghana'],
            ['date' => '2026-06-27 22:00:00', 'a' => 'Aljazair', 'b' => 'Austria'],
            ['date' => '2026-06-27 01:00:00', 'a' => 'Yordania', 'b' => 'Argentina'],
            ['date' => '2026-06-27 04:00:00', 'a' => 'Kolombia', 'b' => 'Portugal'],
            ['date' => '2026-06-27 07:00:00', 'a' => 'RD Kongo', 'b' => 'Uzbekistan'],
        ];

        $venueDefault = 'Swimming Pool - Solo Paragon Hotel & Residences';

        // 3. Eksekusi Looping Data
        foreach ($matches as $match) {
            MatchSchedule::create([
                'team_a' => $match['a'],
                'team_b' => $match['b'],
                'flag_a_url' => $this->getFlagUrl($match['a']),
                'flag_b_url' => $this->getFlagUrl($match['b']),
                'match_date' => $match['date'],
                'venue' => $venueDefault,
                'price' => 150000, // Harga standar 150k
                'quota' => 250,    // Kuota Nobar 250 pax
                'is_hot_match' => $this->isHotMatch($match['a'], $match['b']),
            ]);
        }

        // 4. Panggil Seeder Standing (Klasemen) jika ada
        if (class_exists(StandingSeeder::class)) {
            $this->call([
                StandingSeeder::class,
            ]);
        }
    }
}
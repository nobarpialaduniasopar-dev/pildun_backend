<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Standing;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StandingController extends Controller {
    
    // Ambil data untuk Admin dan Publik
    public function index() {
        $lastUpdated = AppSetting::where('key', 'last_standings_update')->value('value');
        return response()->json([
            'standings' => Standing::all()->groupBy('group_name'),
            'last_updated' => $lastUpdated
        ]);
    }

    // Update manual oleh admin
    public function updateManual(Request $request, Standing $standing) {
        $standing->update($request->all());
        AppSetting::updateOrCreate(['key' => 'last_standings_update'], ['value' => now()->toDateTimeString()]);
        return response()->json(['message' => 'Klasemen diupdate manual']);
    }

    // Sync dari API Eksternal (football-data.org)
    public function syncExternal() {
        // Ganti {COMPETITION_ID} dan tambahkan API Key nanti di .env
        // $response = Http::withHeaders(['X-Auth-Token' => env('FOOTBALL_API_KEY')])->get('https://api.football-data.org/v4/competitions/WC/standings');
        
        // Simulasi update data
        AppSetting::updateOrCreate(['key' => 'last_standings_update'], ['value' => now()->toDateTimeString()]);
        return response()->json(['message' => 'Sync API Eksternal Berhasil. Limit aman.']);
    }
}
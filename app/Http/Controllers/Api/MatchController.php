<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchSchedule;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $hotMatches = MatchSchedule::where('is_hot_match', true)
            ->where('match_date', '>=', now())
            ->orderBy('match_date', 'asc')
            ->get();

        $upcomingMatches = MatchSchedule::where('is_hot_match', false)
            ->where('match_date', '>=', now())
            ->orderBy('match_date', 'asc')
            ->get();

        return response()->json([
            'hot_matches' => $hotMatches,
            'upcoming_matches' => $upcomingMatches
        ]);
    }
}
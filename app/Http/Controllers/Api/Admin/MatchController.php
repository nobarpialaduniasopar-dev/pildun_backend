<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchSchedule;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        return response()->json(MatchSchedule::orderBy('match_date', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_a' => 'required|string',
            'team_b' => 'required|string',
            'flag_a_url' => 'nullable|url',
            'flag_b_url' => 'nullable|url',
            'match_date' => 'required|date',
            'venue' => 'required|string',
            'price' => 'required|integer',
            'quota' => 'required|integer',
            'is_hot_match' => 'boolean'
        ]);

        $match = MatchSchedule::create($validated);
        return response()->json(['message' => 'Jadwal ditambahkan', 'data' => $match], 201);
    }

    public function update(Request $request, $id)
    {
        $match = MatchSchedule::findOrFail($id);
        $match->update($request->all());
        return response()->json(['message' => 'Jadwal diperbarui', 'data' => $match]);
    }

    public function destroy($id)
    {
        MatchSchedule::findOrFail($id)->delete();
        return response()->json(['message' => 'Jadwal dihapus']);
    }
}
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\AppSetting;
use Illuminate\Support\Str;

class ScannerController extends Controller
{
    // ================= ADMIN: MANAJEMEN LINK =================
    public function getToken()
    {
        $setting = AppSetting::firstOrCreate(
            ['key' => 'scanner_auth_token'],
            ['value' => Str::uuid()->toString()]
        );
        return response()->json(['token' => $setting->value]);
    }

    public function generateToken()
    {
        $token = Str::uuid()->toString();
        AppSetting::updateOrCreate(['key' => 'scanner_auth_token'], ['value' => $token]);
        return response()->json(['token' => $token, 'message' => 'Link scanner baru berhasil di-generate. Link lama hangus.']);
    }

    // ================= GATEKEEPER: SCAN & PASS-OUT =================
    private function validateGatekeeperToken($token)
    {
        $validToken = AppSetting::where('key', 'scanner_auth_token')->value('value');
        return $token && $token === $validToken;
    }

    public function scan(Request $request)
    {
        if (!$this->validateGatekeeperToken($request->gatekeeper_token)) {
            return response()->json(['message' => 'UNAUTHORIZED: Link Scanner tidak valid atau sudah hangus!', 'status' => 'error'], 401);
        }

        $request->validate(['ticket_id' => 'required|uuid']);
        $ticket = Ticket::with(['transaction', 'match'])->find($request->ticket_id);

        if (!$ticket) return response()->json(['message' => 'INVALID: Tiket tidak dikenali!', 'status' => 'error'], 404);
        if ($ticket->status === 'CANCELED') return response()->json(['message' => 'REJECTED: Tiket dibatalkan.', 'status' => 'error', 'ticket' => $ticket], 400);
        if ($ticket->status === 'RESERVED') return response()->json(['message' => 'REJECTED: Belum Lunas.', 'status' => 'error', 'ticket' => $ticket], 400);

        if ($ticket->status === 'CHECKED_IN') {
            return response()->json([
                'message' => 'SUDAH CHECK-IN!',
                'status' => 'already_in',
                'ticket' => $ticket
            ], 200);
        }

        $ticket->update(['status' => 'CHECKED_IN', 'scanned_at' => now()]);

        return response()->json([
            'message' => 'AKSES DIBERIKAN',
            'status' => 'success',
            'ticket' => $ticket
        ], 200);
    }

    public function checkout(Request $request)
    {
        if (!$this->validateGatekeeperToken($request->gatekeeper_token)) {
            return response()->json(['message' => 'UNAUTHORIZED: Link Scanner tidak valid!', 'status' => 'error'], 401);
        }

        $request->validate(['ticket_id' => 'required|uuid']);
        $ticket = Ticket::find($request->ticket_id);

        if (!$ticket || $ticket->status !== 'CHECKED_IN') {
            return response()->json(['message' => 'Gagal: Tiket belum di-scan masuk.', 'status' => 'error'], 400);
        }

        $ticket->update(['status' => 'VALID']);

        return response()->json([
            'message' => 'PASS-OUT BERHASIL',
            'status' => 'success'
        ], 200);
    }
}
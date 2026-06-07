<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\AppSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ScannerController extends Controller
{
    // ================= 1. ADMIN: MANAJEMEN LINK =================
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
        return response()->json(['token' => $token, 'message' => 'Link scanner baru berhasil dibuat. Link lama otomatis hangus!']);
    }

    // ================= 2. GATEKEEPER: SCAN (PUBLIC + TOKEN) =================
    public function scanGatekeeper(Request $request)
    {
        // 1. Validasi Magic Link Token
        $validToken = AppSetting::where('key', 'scanner_auth_token')->value('value');
        if (!$request->gatekeeper_token || $request->gatekeeper_token !== $validToken) {
            return response()->json(['message' => 'UNAUTHORIZED: Link Scanner tidak valid atau sudah ditarik oleh Admin!', 'status' => 'error'], 401);
        }

        // 2. Validasi Format QR
        $validator = Validator::make($request->all(), [
            'ticket_code' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Format QR Tidak Dikenal!', 'status' => 'error'], 400);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 3. PESSIMISTIC LOCKING: Kunci data saat dibaca
                $ticket = Ticket::with(['transaction'])->where('id', $request->ticket_code)->lockForUpdate()->first();

                if (!$ticket) return response()->json(['message' => 'INVALID: Tiket tidak ada di database!', 'status' => 'error'], 404);
                if ($ticket->status === 'CANCELED') return response()->json(['message' => 'REJECTED: Tiket telah dibatalkan.', 'status' => 'error'], 422);
                if ($ticket->status === 'RESERVED') return response()->json(['message' => 'REJECTED: Belum lunas.', 'status' => 'error'], 422);

                if ($ticket->status === 'CHECKED_IN') {
                    return response()->json([
                        'message' => 'TIKET SUDAH DIGUNAKAN!',
                        'status' => 'already_in',
                        'scanned_at' => $ticket->scanned_at
                    ], 422);
                }

                // 4. Buka Pintu
                $ticket->update(['status' => 'CHECKED_IN', 'scanned_at' => now()]);

                return response()->json([
                    'message' => 'CHECK-IN BERHASIL!',
                    'status' => 'success',
                    'data' => [
                        'buyer_name' => $ticket->transaction->buyer_name ?? 'Unknown',
                        'qty' => 1
                    ]
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Kesalahan server internal.'], 500);
        }
    }
}
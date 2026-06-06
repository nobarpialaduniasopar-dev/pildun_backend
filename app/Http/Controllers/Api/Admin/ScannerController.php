<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate(['ticket_id' => 'required|uuid']);

        // Menggunakan with() untuk anti N+1 saat memunculkan info tiket
        $ticket = Ticket::with(['matchSchedule', 'transaction'])->find($request->ticket_id);

        if (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        if ($ticket->status === 'CANCELED' || $ticket->status === 'RESERVED') {
            return response()->json(['message' => 'Tiket tidak valid (Status: ' . $ticket->status . ')'], 400);
        }

        if ($ticket->status === 'CHECKED_IN') {
            return response()->json([
                'status' => 'warning',
                'message' => 'Tiket sudah discan pada ' . $ticket->scanned_at->format('d M Y H:i:s'),
                'ticket_id' => $ticket->id
            ], 409); // 409 Conflict memicu dialog peringatan di Frontend
        }

        // Normal Scan In
        $ticket->update([
            'status' => 'CHECKED_IN',
            'scanned_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Akses Diberikan',
            'data' => $ticket
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate(['ticket_id' => 'required|uuid']);
        
        $ticket = Ticket::find($request->ticket_id);
        
        if (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        // Reset status agar bisa discan ulang
        $ticket->update([
            'status' => 'VALID',
            'scanned_at' => null
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Penonton telah di-Checkout. Tiket bisa digunakan lagi.'
        ]);
    }
}
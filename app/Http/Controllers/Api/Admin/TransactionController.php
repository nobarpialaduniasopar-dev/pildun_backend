<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('matchSchedule')->orderBy('created_at', 'desc');
        
        if ($request->has('match_id')) {
            $query->where('match_id', $request->match_id);
        }

        return response()->json($query->paginate(20));
    }

    public function exportCsv(Request $request)
    {
        $query = Transaction::with('matchSchedule')->orderBy('created_at', 'desc');
        if ($request->has('match_id')) {
            $query->where('match_id', $request->match_id);
        }
        $transactions = $query->get();

        $filename = "Export_Transaksi_" . now()->format('Y-m-d_H-i') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Match', 'Nama Pembeli', 'Email', 'WhatsApp', 'Qty', 'Total', 'Status', 'Tanggal']);

            foreach ($transactions as $trx) {
                $matchName = $trx->matchSchedule->team_a . ' VS ' . $trx->matchSchedule->team_b;
                fputcsv($file, [
                    $trx->id, $matchName, $trx->buyer_name, $trx->buyer_email, 
                    $trx->buyer_whatsapp, $trx->qty, $trx->total_amount, 
                    $trx->payment_status, $trx->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
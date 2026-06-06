<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\MatchSchedule;
use App\Models\Ticket;

class ReleaseExpiredTicket implements ShouldQueue
{
    use Queueable;

    public $transactionId;

    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function handle(): void
    {
        DB::transaction(function () {
            // Ambil transaksi, lock untuk memastikan data tidak berubah saat worker berjalan
            $transaction = Transaction::where('id', $this->transactionId)->lockForUpdate()->first();

            // Eksekusi HANYA JIKA status masih PENDING
            if ($transaction && $transaction->payment_status === 'PENDING') {
                
                $transaction->update(['payment_status' => 'EXPIRED']);
                
                // Ubah status tiket
                Ticket::where('transaction_id', $transaction->id)->update(['status' => 'CANCELED']);
                
                // Kembalikan master stok
                $match = MatchSchedule::where('id', $transaction->match_id)->lockForUpdate()->first();
                if ($match) {
                    $match->increment('quota', $transaction->qty);
                }
            }
        });
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\MatchSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

class WebhookController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        
        // Validasi Signature Key (Keamanan Mutlak Midtrans)
        $signatureKey = hash("sha512", $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . Config::$serverKey);
        
        if ($signatureKey !== $payload['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'];
        $orderId = $payload['order_id'];

        DB::transaction(function () use ($orderId, $transactionStatus) {
            $transaction = Transaction::where('id', $orderId)->lockForUpdate()->first();

            if (!$transaction) return;

            // Jika status sukses
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($transaction->payment_status !== 'PAID') {
                    $transaction->update(['payment_status' => 'PAID']);
                    Ticket::where('transaction_id', $transaction->id)->update(['status' => 'VALID']);
                    
                    $this->sendETickets($transaction);
                }
            } 
            // Jika status gagal/kadaluarsa dari Midtrans
            elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                if ($transaction->payment_status === 'PENDING') {
                    $transaction->update(['payment_status' => 'CANCELED']);
                    Ticket::where('transaction_id', $transaction->id)->update(['status' => 'CANCELED']);
                    
                    $match = MatchSchedule::where('id', $transaction->match_id)->lockForUpdate()->first();
                    if ($match) {
                        $match->increment('quota', $transaction->qty);
                    }
                }
            }
        });

        return response()->json(['message' => 'Webhook processed']);
    }

    private function sendETickets(Transaction $transaction)
    {
        $tickets = Ticket::where('transaction_id', $transaction->id)->get();
        $resend = \Resend::client(env('RESEND_API_KEY'));

        $htmlContent = "<h2>Pembayaran Berhasil! Ini E-Ticket Anda:</h2>";
        
        foreach ($tickets as $index => $ticket) {
            // Gunakan API publik untuk generate QR Image dari UUID tiket
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$ticket->id}";
            $num = $index + 1;
            
            $htmlContent .= "
                <div style='border: 1px solid #3CAC3B; padding: 15px; margin-bottom: 20px;'>
                    <h3>Tiket #{$num}</h3>
                    <img src='{$qrUrl}' alt='QR Code Ticket' />
                    <p>ID: {$ticket->id}</p>
                    <p><strong>Harap tunjukkan QR ini di pintu masuk.</strong></p>
                </div>
            ";
        }

        try {
            $resend->emails->send([
                'from' => 'onboarding@resend.dev', // Sesuai instruksi SOT testing
                'to' => [$transaction->buyer_email],
                'subject' => 'E-Ticket Nobar Piala Dunia - Solo Paragon',
                'html' => $htmlContent,
            ]);
        } catch (\Exception $e) {
            // Log error pengiriman email jika perlu, tapi jangan batalkan transaksi
            Log::error('Gagal kirim E-Ticket ke ' . $transaction->buyer_email . ': ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchSchedule;
use App\Models\Transaction;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\CoreApi;
use App\Jobs\ReleaseExpiredTicket;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function process(Request $request)
    {
        // 1. Validasi Input Strict
        $validated = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'required|email',
            'buyer_whatsapp' => 'required|string',
            'buyer_instagram' => 'nullable|string',
            'buyer_age' => 'required|integer',
            'qty' => 'required|integer|min:1|max:5',
            'payment_type' => 'required|in:bank_transfer,gopay,qris',
            'bank' => 'required_if:payment_type,bank_transfer|nullable|in:bca,bni,bri,mandiri',
        ]);

        try {
            // 2. Database Transaction & Pessimistic Locking
            $transaction = DB::transaction(function () use ($validated) {
                // LOCK baris jadwal pertandingan ini
                $match = MatchSchedule::where('id', $validated['match_id'])->lockForUpdate()->first();

                if ($match->quota < $validated['qty']) {
                    throw new \Exception('Stok tiket tidak mencukupi atau sedang dikunci pembeli lain.', 400);
                }

                // Kurangi stok utama sementara
                $match->decrement('quota', $validated['qty']);

                $totalAmount = $match->price * $validated['qty'];
                $orderId = (string) \Illuminate\Support\Str::uuid();

                // Buat Data Transaksi (PENDING)
                $trx = Transaction::create([
                    'id' => $orderId,
                    'match_id' => $match->id,
                    'buyer_name' => $validated['buyer_name'],
                    'buyer_email' => $validated['buyer_email'],
                    'buyer_whatsapp' => $validated['buyer_whatsapp'],
                    'buyer_instagram' => $validated['buyer_instagram'],
                    'buyer_age' => $validated['buyer_age'],
                    'qty' => $validated['qty'],
                    'total_amount' => $totalAmount,
                    'payment_method' => $validated['payment_type'] . ($validated['bank'] ? '_' . $validated['bank'] : ''),
                    'payment_status' => 'PENDING',
                    'locked_until' => now()->addMinutes(15), // Batas Bayar
                ]);

                // Buat Tiket (RESERVED) - Loop sebanyak Qty
                for ($i = 0; $i < $validated['qty']; $i++) {
                    Ticket::create([
                        'transaction_id' => $trx->id,
                        'match_id' => $match->id,
                        'status' => 'RESERVED',
                    ]);
                }

                return $trx;
            });

            // 3. Tembak Midtrans Core API
            $paymentParams = [
                'payment_type' => $validated['payment_type'],
                'transaction_details' => [
                    'order_id' => $transaction->id,
                    'gross_amount' => $transaction->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $transaction->buyer_name,
                    'email' => $transaction->buyer_email,
                    'phone' => $transaction->buyer_whatsapp,
                ],
            ];

            // Tambahkan param spesifik bank transfer jika dipilih
            if ($validated['payment_type'] == 'bank_transfer') {
                $paymentParams['bank_transfer'] = [
                    'bank' => $validated['bank']
                ];
            }

            $midtransResponse = CoreApi::charge($paymentParams);

            // 4. Update instruksi pembayaran ke database
            $paymentUrlOrVa = null;
            if ($validated['payment_type'] == 'bank_transfer' && isset($midtransResponse->va_numbers[0])) {
                $paymentUrlOrVa = $midtransResponse->va_numbers[0]->va_number;
            } elseif ($validated['payment_type'] == 'qris' && isset($midtransResponse->actions[0])) {
                $paymentUrlOrVa = $midtransResponse->actions[0]->url; // URL image QR
            }

            $transaction->update([
                'payment_url_or_va' => $paymentUrlOrVa
            ]);

            // Pemicu Worker: Batalkan transaksi otomatis 15 menit dari sekarang jika PENDING
            ReleaseExpiredTicket::dispatch($transaction->id)->delay(now()->addMinutes(15));

            return response()->json([
                'message' => 'Checkout Success',
                'order_id' => $transaction->id,
                'total_amount' => $transaction->total_amount,
                'payment_method' => $transaction->payment_method,
                'instruction' => $paymentUrlOrVa,
                'locked_until' => $transaction->locked_until,
            ]);

        } catch (\Exception $e) {
            $code = $e->getCode() == 400 ? 400 : 500;
            return response()->json(['message' => $e->getMessage()], $code);
        }
    }

    public function show($order_id)
    {
        // Gunakan eager loading dengan matchSchedule untuk mencegah masalah N+1
        $transaction = Transaction::with('matchSchedule')->find($order_id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaction);
    }
}
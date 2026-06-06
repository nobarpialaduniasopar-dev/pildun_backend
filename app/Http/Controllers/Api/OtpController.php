<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use Illuminate\Http\Request;
use Resend\Laravel\Facades\Resend; // Asumsi menggunakan facade jika di-setup, atau native client

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Otp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp_code' => $code,
                'expires_at' => now()->addMinutes(5),
                'is_used' => false
            ]
        );

        // Integrasi Resend (Ganti dengan client native jika facade tidak terdaftar)
        $resend = \Resend::client(env('RESEND_API_KEY'));
        $resend->emails->send([
            'from' => 'TicketGo <no-reply@domain-anda.com>', // Sesuaikan domain Resend Anda
            'to' => [$request->email],
            'subject' => 'Kode OTP Pembelian Tiket Nobar',
            'html' => "<strong>Kode OTP Anda: {$code}</strong>. Berlaku selama 5 menit.",
        ]);

        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|digits:6'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'OTP Invalid or Expired'], 400);
        }

        $otp->update(['is_used' => true]);

        // Berikan token sementara atau biarkan frontend lanjut membawa email yang sudah terverifikasi
        return response()->json(['message' => 'OTP Verified']);
    }
}
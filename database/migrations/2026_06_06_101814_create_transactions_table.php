<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Midtrans Order ID
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade')->index();
            $table->string('buyer_name');
            $table->string('buyer_email')->index();
            $table->string('buyer_whatsapp');
            $table->string('buyer_instagram')->nullable();
            $table->integer('buyer_age');
            $table->integer('qty')->default(1);
            $table->integer('total_amount');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['PENDING', 'PAID', 'EXPIRED', 'CANCELED'])->default('PENDING')->index();
            $table->string('midtrans_snap_token')->nullable();
            $table->text('payment_url_or_va')->nullable();
            $table->timestamp('locked_until')->nullable(); // Batas 15 menit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

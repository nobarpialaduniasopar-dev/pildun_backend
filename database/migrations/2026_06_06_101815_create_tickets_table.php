<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Payload QR Code
            // Deklarasi kolom & index secara eksplisit
            $table->uuid('transaction_id')->index();
            $table->unsignedBigInteger('match_id')->index();
            
            // Deklarasi relasi foreign key secara eksplisit
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            
            $table->enum('status', ['RESERVED', 'VALID', 'CHECKED_IN', 'CANCELED'])->default('RESERVED')->index();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

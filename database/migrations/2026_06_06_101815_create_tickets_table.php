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
            $table->foreignUuid('transaction_id')->constrained('transactions')->onDelete('cascade')->index();
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade')->index();
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

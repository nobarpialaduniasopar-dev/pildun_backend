<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan nama tabel diubah menjadi 'matches'
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('team_a');
            $table->string('team_b');
            $table->string('flag_a_url')->nullable();
            $table->string('flag_b_url')->nullable();
            $table->timestamp('match_date')->index();
            $table->string('venue')->default('Solo Paragon');
            $table->integer('price');
            $table->integer('quota'); // Master stok untuk locking
            $table->boolean('is_hot_match')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};

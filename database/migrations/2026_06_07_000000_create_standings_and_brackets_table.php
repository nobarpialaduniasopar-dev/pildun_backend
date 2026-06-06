<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->string('team_name');
            $table->string('flag_url')->nullable();
            $table->integer('played')->default(0);
            $table->integer('won')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('points')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->timestamps();
        });

        Schema::create('brackets', function (Blueprint $table) {
            $table->id();
            $table->string('round'); // e.g., 16, Quarter, Semi, Final
            $table->foreignId('match_id')->nullable()->constrained('match_schedules')->nullOnDelete();
            $table->foreignId('next_match_id')->nullable()->constrained('match_schedules')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('brackets');
        Schema::dropIfExists('standings');
    }
};
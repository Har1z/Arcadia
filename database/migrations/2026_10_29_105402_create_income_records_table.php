<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('income_records', function (Blueprint $table) {
            $table->id();
            // Income follows its machine: deleting a machine removes its income history.
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            // restrictOnDelete: a user who still has records cannot be deleted.
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->unsignedInteger('play_count');
            $table->decimal('total_income', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_records');
    }
};

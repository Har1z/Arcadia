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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            // Logs follow their machine: deleting a machine removes its history.
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            // restrictOnDelete: a user who still has logs cannot be deleted,
            // the controller shows a friendly error instead of a database crash.
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('issue');
            $table->text('action_taken')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            // Status values ('open'/'done') are enforced by validation rules.
            $table->string('status')->default('open');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};

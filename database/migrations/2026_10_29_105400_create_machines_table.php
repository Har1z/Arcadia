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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            // restrictOnDelete: a category that still has machines cannot be deleted,
            // so machines never end up with a dangling category_id.
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('serial_number')->unique();
            $table->string('location');
            $table->date('purchase_date');
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('price_per_play', 10, 2);
            // Status values ('active'/'maintenance'/'broken'/'retired') are enforced
            // by validation rules in the form requests.
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};

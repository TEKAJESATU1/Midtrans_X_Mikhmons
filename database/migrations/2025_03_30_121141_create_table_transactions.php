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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->string('status')->default('pending');
            $table->integer('price');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('profile');
            $table->integer('duration');
            $table->text('payload')->nullable(); // Untuk menyimpan response dari Midtrans
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

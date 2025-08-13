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
            $table->id(); // Primary key auto-increment
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade'); // Foreign key ke tabel bills
            $table->json('midtrans_response')->nullable(); // Menyimpan response Midtrans dalam format JSON
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending'); // Status transaksi
            $table->timestamp('paid_at')->nullable(); // Waktu pembayaran dilakukan
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

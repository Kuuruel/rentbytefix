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
        Schema::create('bills', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Foreign key ke tabel tenants
            $table->foreignId('renter_id')->constrained('renters')->onDelete('cascade'); // Foreign key ke tabel renters
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade'); // Foreign key ke tabel properties
            $table->decimal('amount', 15, 2); // Jumlah tagihan, dengan 15 digit total dan 2 di belakang koma
            $table->date('due_date'); // Tanggal jatuh tempo pembayaran
            $table->string('payment_link')->nullable(); // Link pembayaran dari Midtrans
            $table->enum('status', ['pending', 'paid', 'cancelled', 'overdue'])->default('pending'); // Status tagihan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};

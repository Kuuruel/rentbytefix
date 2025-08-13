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
        Schema::create('renters', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Foreign key ke tabel tenants
            $table->string('name'); // Nama penyewa
            $table->string('phone')->nullable(); // Nomor telepon penyewa
            $table->string('email')->nullable(); // Alamat email penyewa
            $table->foreignId('unit_id')->nullable()->constrained('properties')->onDelete('set null'); // Foreign key ke tabel properties (unit yang disewa)
            $table->date('start_date'); // Tanggal mulai sewa
            $table->date('end_date'); // Tanggal berakhir sewa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renters');
    }
};

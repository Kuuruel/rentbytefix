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
        Schema::create('properties', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Foreign key ke tabel tenants
            $table->string('name'); // Nama properti (misal: "Apartemen A", "Rumah B")
            $table->string('type')->nullable(); // Tipe properti (misal: "Apartemen", "Rumah", "Kantor")
            $table->string('address')->nullable(); // Alamat lengkap properti
            $table->decimal('price', 15, 2); // Harga sewa properti, dengan 15 digit total dan 2 di belakang koma
            $table->enum('rent_type', ['monthly', 'yearly']); // Tipe sewa: bulanan atau tahunan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};

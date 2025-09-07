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
        Schema::table('properties', function (Blueprint $table) {
            // Hapus constraint enum lama
            $table->dropColumn('status');
        });

        Schema::table('properties', function (Blueprint $table) {
            // Tambahkan kembali dengan enum yang baru
            $table->enum('status', ['Available', 'Processing', 'Rented'])->default('Available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('properties', function (Blueprint $table) {
            // Kembalikan ke enum lama
            $table->enum('status', ['Available', 'Rented'])->default('Available');
        });
    }
};
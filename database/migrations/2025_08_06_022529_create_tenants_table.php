<?php
// database/migrations/2025_08_06_022529_create_tenants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('note')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->string('avatar')->nullable();
            $table->string('country', 100);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['status']);
            $table->index(['created_at']);
            $table->index(['name']);
            $table->index(['country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
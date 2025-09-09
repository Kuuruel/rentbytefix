<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midtrans_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->default('guest');
            $table->longText('draft_data');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midtrans_drafts');
    }
};

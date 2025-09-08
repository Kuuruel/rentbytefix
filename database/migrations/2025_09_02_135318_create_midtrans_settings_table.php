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
        Schema::create('midtrans_settings', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id', 255)->comment('Midtrans Merchant ID');
            $table->string('client_key', 255)->comment('Midtrans Client Key');
            $table->string('server_key', 255)->comment('Midtrans Server Key');
            $table->enum('environment', ['sandbox', 'production'])
                ->default('sandbox')
                ->comment('Midtrans Environment');
            $table->text('webhook_url')->nullable()->comment('Webhook/Notification URL');
            $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
            $table->timestamps();

            // Index untuk performa
            $table->index('environment');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midtrans_settings');
    }
};

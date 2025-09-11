<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('user_id')->nullable(); // NULLABLE untuk tenant
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamp('read_at');
            $table->timestamps();

            // Foreign keys
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Unique constraint yang fleksibel
            $table->unique(['notification_id', 'user_id', 'tenant_id'], 'unique_notification_read');

            // Indexes untuk performance
            $table->index(['user_id', 'read_at']);
            $table->index(['tenant_id', 'read_at']);
            $table->index(['notification_id', 'tenant_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_reads');
    }
};

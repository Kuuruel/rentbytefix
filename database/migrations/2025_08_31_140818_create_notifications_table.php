<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['Normal', 'Important', 'Critical'])->default('Normal');
            $table->json('delivery_methods'); // ['Dashboard', 'Email', 'Push Notifications']
            $table->enum('target_type', ['all', 'specific'])->default('all');
            $table->json('target_tenant_ids')->nullable(); // [1,2,3] untuk specific tenants
            $table->boolean('is_archived')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['is_archived', 'created_at']);
            $table->index(['target_type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};

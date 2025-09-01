<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('default_priority', ['Normal', 'Important', 'Critical'])->default('Normal');
            $table->json('default_delivery_methods'); // ['Dashboard', 'Email', 'Push Notifications']
            $table->string('email_from')->nullable();
            $table->string('email_footer')->nullable();
            $table->boolean('push_enabled')->default(true);
            $table->integer('dashboard_display_count')->default(5);
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_settings');
    }
};

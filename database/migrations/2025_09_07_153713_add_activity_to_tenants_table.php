<?php
// database/migrations/xxxx_add_activity_to_tenants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('activity_type')->default('create'); // create, update, delete
            $table->string('activity_by')->nullable(); // nama user
        });
    }

    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['activity_type', 'activity_by']);
        });
    }
};

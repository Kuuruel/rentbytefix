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
<<<<<<<< HEAD:database/migrations/2025_09_01_051932_add_order_id_to_bills_table.php
        Schema::table('bills', function (Blueprint $table) {
            $table->string('order_id')->unique()->after('id');
========
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom role sudah ada atau belum
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'landlord'])->default('landlord');
            }
>>>>>>>> origin/backup2:database/migrations/2025_08_08_063704_add_role_to_users_table.php
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2025_09_01_051932_add_order_id_to_bills_table.php
        Schema::table('bills', function (Blueprint $table) {
            //
========
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom role ada sebelum di-drop
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
>>>>>>>> origin/backup2:database/migrations/2025_08_08_063704_add_role_to_users_table.php
        });
    }
};

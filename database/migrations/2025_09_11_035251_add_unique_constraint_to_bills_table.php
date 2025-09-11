<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            // 🔥 TAMBAH UNIQUE CONSTRAINT untuk mencegah duplicate
            // Format: tenant_id + renter_id + property_id + amount + due_date harus unique
            $table->unique([
                'tenant_id',
                'renter_id',
                'property_id',
                'amount',
                'due_date'
            ], 'bills_no_duplicate_constraint');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropUnique('bills_no_duplicate_constraint');
        });
    }
};

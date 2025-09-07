<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'order_id')) {
                $table->string('order_id')->nullable()->after('bill_id');
            }
            if (!Schema::hasColumn('transactions', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0)->after('order_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'order_id')) {
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('transactions', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};

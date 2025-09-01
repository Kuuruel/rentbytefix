<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;
use App\Models\User;

class NotificationSettingSeeder extends Seeder
{
    public function run()
    {
        // Cari admin user atau buat default
        $adminUser = User::where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : 1;

        NotificationSetting::create([
            'default_priority' => 'Normal',
            'default_delivery_methods' => ['Dashboard'],
            'email_from' => 'admin@system.com',
            'email_footer' => 'System Administrator',
            'push_enabled' => true,
            'dashboard_display_count' => 5,
            'updated_by' => $adminId
        ]);
    }
}

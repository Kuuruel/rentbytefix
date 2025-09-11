<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MidtransSetting;

class CheckMidtransSettings extends Command
{
    protected $signature = 'midtrans:check';

    protected $description = 'Check Midtrans settings in database';

    public function handle()
    {
        $this->info('🔍 Checking Midtrans Settings...');
        $this->newLine();

        $settings = MidtransSetting::first();

        if ($settings) {
            $this->info('✅ Midtrans settings found!');
            $this->newLine();

            $this->table(
                ['Field', 'Value', 'Status'],
                [
                    ['ID', $settings->id, '✅'],
                    ['Merchant ID', $settings->merchant_id, $settings->merchant_id ? '✅' : '❌'],
                    ['Client Key', $settings->client_key, $settings->client_key ? '✅' : '❌'],
                    ['Server Key', $settings->server_key ? '***masked***' : 'Not set', $settings->server_key ? '✅' : '❌'],
                    ['Environment', $settings->environment, $settings->environment ? '✅' : '❌'],
                    ['Webhook URL', $settings->webhook_url ?: 'Not set', $settings->webhook_url ? '✅' : '⚠️'],
                    ['Active', $settings->is_active ? 'Yes' : 'No', $settings->is_active ? '✅' : '❌'],
                    ['Created At', $settings->created_at, '✅'],
                    ['Updated At', $settings->updated_at, '✅'],
                ]
            );

            $this->newLine();

            if ($settings->environment === 'production') {
                $this->warn('⚠️  Production environment detected!');
            } else {
                $this->info('🧪 Sandbox environment - safe for testing');
            }
        } else {
            $this->warn('❌ No Midtrans settings found in database');
            $this->newLine();
            $this->info('💡 To create settings, visit: /admin/midtrans-settings');
        }

        $this->newLine();
        $this->info('📊 Total records: ' . MidtransSetting::count());
    }
}

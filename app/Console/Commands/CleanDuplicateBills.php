<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;

class CleanDuplicateBills extends Command
{

    protected $signature = 'bills:clean-duplicates {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Clean duplicate bills from database';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🔍 Scanning for duplicate bills...');

        
        $duplicates = Bill::select('tenant_id', 'renter_id', 'property_id', 'amount', 'due_date')
            ->selectRaw('COUNT(*) as duplicate_count, GROUP_CONCAT(id ORDER BY created_at) as ids')
            ->groupBy('tenant_id', 'renter_id', 'property_id', 'amount', 'due_date')
            ->having('duplicate_count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate bills found!');
            return 0;
        }

        $this->warn("⚠️ Found {$duplicates->count()} sets of duplicate bills:");

        $totalToDelete = 0;
        $deletedCount = 0;

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $keepId = array_shift($ids); 
            $totalToDelete += count($ids);

            $this->line("📋 Tenant: {$duplicate->tenant_id}, Amount: {$duplicate->amount}, Due: {$duplicate->due_date}");
            $this->line("   └── Keep: #{$keepId}, Delete: #" . implode(', #', $ids));

            if (!$isDryRun) {
                Bill::whereIn('id', $ids)->delete();
                $deletedCount += count($ids);
            }
        }

        if ($isDryRun) {
            $this->warn("🔥 DRY RUN: Would delete {$totalToDelete} duplicate bills");
            $this->info("💡 Run without --dry-run to actually delete them");
        } else {
            $this->info("✅ Successfully deleted {$deletedCount} duplicate bills");
        }

        return 0;
    }
}

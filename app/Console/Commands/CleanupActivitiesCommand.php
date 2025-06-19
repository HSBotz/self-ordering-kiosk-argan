<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupActivitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:cleanup {--days=30 : Jumlah hari aktivitas yang dipertahankan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan aktivitas lama dari database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Membersihkan aktivitas yang lebih lama dari {$days} hari ({$cutoffDate})...");

        try {
            $count = Activity::where('created_at', '<', $cutoffDate)->delete();

            $this->info("Berhasil menghapus {$count} aktivitas lama.");
            Log::info("CleanupActivitiesCommand: Berhasil menghapus {$count} aktivitas lama (lebih dari {$days} hari).");

            return 0;
        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan: {$e->getMessage()}");
            Log::error("CleanupActivitiesCommand error: {$e->getMessage()}");

            return 1;
        }
    }
}

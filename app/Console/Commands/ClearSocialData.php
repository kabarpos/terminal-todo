<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MetricData;
use App\Models\SocialAccount;
use App\Models\MetricTarget;
use Illuminate\Support\Facades\DB;

class ClearSocialData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:clear {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus semua data metrik dan akun sosial';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin menghapus SEMUA data metrik dan akun sosial? Operasi ini tidak dapat dibatalkan!')) {
            $this->info('Operasi dibatalkan.');
            return;
        }

        try {
            // Matikan foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $metricCount = MetricData::count();
            $accountCount = SocialAccount::count();
            $targetCount = MetricTarget::count();

            // Hapus data dari tabel-tabel terkait
            MetricData::truncate();
            $this->info("✓ Berhasil menghapus {$metricCount} data metrik.");

            MetricTarget::truncate();
            $this->info("✓ Berhasil menghapus {$targetCount} data target metrik.");

            SocialAccount::truncate();
            $this->info("✓ Berhasil menghapus {$accountCount} akun sosial.");

            // Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('Semua data berhasil dihapus!');

        } catch (\Exception $e) {
            // Pastikan foreign key checks diaktifkan kembali meskipun terjadi error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

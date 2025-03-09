<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SocialAccount;
use App\Services\MetricScraperService;

class FetchMetricDataCommand extends Command
{
    protected $signature = 'metrics:fetch';
    protected $description = 'Mengambil data metrik dari semua akun sosial media';

    public function handle()
    {
        $accounts = SocialAccount::all();
        $scraper = new MetricScraperService();

        foreach ($accounts as $account) {
            $this->info("Mengambil data untuk {$account->name} ({$account->platform->name})");
            
            try {
                $metrics = $scraper->scrapeMetrics($account);
                // Simpan data metrik
                $account->metrics()->create($metrics);
                $this->info("Berhasil mengambil data untuk {$account->name}");
            } catch (\Exception $e) {
                $this->error("Gagal mengambil data untuk {$account->name}: " . $e->getMessage());
            }
        }
    }
} 
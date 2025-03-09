<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SocialAccount;
use App\Models\MetricData;
use Carbon\Carbon;

class FetchMetricData extends Command
{
    protected $signature = 'metrics:fetch';
    protected $description = 'Mengambil data metrik dari semua akun sosial media';

    public function handle()
    {
        $accounts = SocialAccount::with('platform')->get();
        $date = now()->format('Y-m-d');

        foreach ($accounts as $account) {
            $this->info("Mengambil data untuk {$account->platform->name} - {$account->name}");

            // Cek apakah data untuk hari ini sudah ada
            $exists = MetricData::where('social_account_id', $account->id)
                ->whereDate('date', $date)
                ->exists();

            if ($exists) {
                $this->warn("Data untuk {$account->name} tanggal {$date} sudah ada, melewati...");
                continue;
            }

            try {
                // Di sini Anda bisa menambahkan logika untuk mengambil data dari masing-masing platform
                // Contoh implementasi untuk masing-masing platform:
                switch ($account->platform->name) {
                    case 'Instagram':
                        $data = $this->fetchInstagramData($account);
                        break;
                    case 'Facebook':
                        $data = $this->fetchFacebookData($account);
                        break;
                    case 'Twitter':
                        $data = $this->fetchTwitterData($account);
                        break;
                    // Tambahkan platform lain sesuai kebutuhan
                    default:
                        $this->warn("Platform {$account->platform->name} belum didukung");
                        continue;
                }

                if ($data) {
                    MetricData::create([
                        'social_account_id' => $account->id,
                        'date' => $date,
                        'followers_count' => $data['followers_count'] ?? 0,
                        'engagement_rate' => $data['engagement_rate'] ?? 0,
                        'reach' => $data['reach'] ?? 0,
                        'impressions' => $data['impressions'] ?? 0,
                        'likes' => $data['likes'] ?? 0,
                        'comments' => $data['comments'] ?? 0,
                        'shares' => $data['shares'] ?? 0
                    ]);

                    $this->info("✓ Data berhasil disimpan untuk {$account->name}");
                }
            } catch (\Exception $e) {
                $this->error("Gagal mengambil data untuk {$account->name}: " . $e->getMessage());
            }
        }
    }

    protected function fetchInstagramData($account)
    {
        // Implementasi pengambilan data dari Instagram API
        // Anda perlu mengimplementasikan ini sesuai dengan API yang Anda gunakan
        return null;
    }

    protected function fetchFacebookData($account)
    {
        // Implementasi pengambilan data dari Facebook API
        return null;
    }

    protected function fetchTwitterData($account)
    {
        // Implementasi pengambilan data dari Twitter API
        return null;
    }
} 
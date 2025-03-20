<?php

namespace App\Console\Commands;

use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanDeletedMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:clean {days=30 : Berapa hari setelah softdelete file akan dihapus permanen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan file media yang sudah dihapus (softdelete) setelah periode tertentu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');
        $this->info("Mencari file media yang di-softdelete lebih dari {$days} hari...");

        // Cari media yang di-softdelete lebih dari X hari yang lalu
        $cutoffDate = Carbon::now()->subDays($days);
        $deletedMedia = Media::onlyTrashed()
            ->where('deleted_at', '<=', $cutoffDate)
            ->get();

        if ($deletedMedia->isEmpty()) {
            $this->info('Tidak ada file yang perlu dihapus.');
            return;
        }

        $this->info("Ditemukan {$deletedMedia->count()} file yang akan dihapus permanen.");
        
        $bar = $this->output->createProgressBar($deletedMedia->count());
        $bar->start();
        
        $success = 0;
        $failed = 0;
        
        foreach ($deletedMedia as $media) {
            // Hapus file fisik dari storage
            try {
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                    
                    // Hapus data dari database secara permanen
                    $media->forceDelete();
                    $success++;
                } else {
                    // Jika file tidak ada, hapus saja record database
                    $media->forceDelete();
                    $success++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("Gagal menghapus media ID: {$media->id} - {$e->getMessage()}");
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Berhasil menghapus {$success} file.");
        if ($failed > 0) {
            $this->warn("Gagal menghapus {$failed} file.");
        }
    }
} 
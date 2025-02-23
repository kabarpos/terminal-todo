<?php

namespace App\Listeners;

use App\Events\UserStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HandleUserStatusChange
{
    public function handle(UserStatusChanged $event): void
    {
        if ($event->newStatus !== 'active') {
            // Hapus semua sesi user yang dinonaktifkan
            DB::table('sessions')
                ->where('user_id', $event->user->id)
                ->delete();
        }
    }
} 
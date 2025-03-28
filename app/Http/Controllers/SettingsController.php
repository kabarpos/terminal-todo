<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-settings');
    }

    public function index()
    {
        // Ambil settings yang dikelompokkan untuk form
        $groupedSettings = Setting::orderBy('group')->get()->groupBy('group');
        
        // Ambil current values dari cache untuk konsistensi
        $currentSettings = Cache::get('website_settings', []);
        
        // Update nilai di grouped settings dengan current value
        foreach ($groupedSettings as $group => $settings) {
            foreach ($settings as $setting) {
                $setting->value = $currentSettings[$setting->key] ?? $setting->value;
            }
        }
        
        return Inertia::render('Settings/Index', [
            'settings' => $groupedSettings,
        ]);
    }

    public function update(Request $request)
    {
        $updatedSettings = [];
        
        foreach ($request->all() as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                if ($setting->type == 'file' && $value instanceof \Illuminate\Http\UploadedFile) {
                    // Hapus file lama jika ada
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    // Upload file baru
                    $path = $value->store('settings', 'public');
                    $value = $path;
                }
                
                Setting::set($key, $value);
                $updatedSettings[$key] = $value;
            }
        }

        // Clear cache
        Cache::forget('website_settings');
        
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui');
    }
} 
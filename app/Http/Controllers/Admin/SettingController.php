<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->get()->groupBy('group');
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'current_settings' => Cache::get('website_settings', [])
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
                    
                    // Upload file baru dengan nama yang unik
                    $path = $value->store('settings', 'public');
                    $value = $path;
                }
                
                Setting::set($key, $value);
                $updatedSettings[$key] = $value;
            }
        }

        // Clear cache setelah update settings
        Cache::forget('website_settings');
        
        // Set cache baru dengan settings yang sudah diupdate
        Cache::remember('website_settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        // Redirect dengan data baru
        return redirect()->back()->with([
            'success' => 'Settings updated successfully',
            'settings' => $updatedSettings
        ]);
    }
} 
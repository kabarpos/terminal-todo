<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\Platform;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SocialAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = SocialAccount::with('platform')
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($request->platform_id, function ($query, $platformId) {
                return $query->where('platform_id', $platformId);
            })
            ->when($request->status === 'active', function ($query) {
                return $query->where('is_active', true);
            })
            ->when($request->status === 'inactive', function ($query) {
                return $query->where('is_active', false);
            });

        return Inertia::render('SocialAccounts/Index', [
            'accounts' => $query->paginate(10),
            'platforms' => Platform::where('is_active', true)->get(),
            'filters' => $request->only(['search', 'platform_id', 'status'])
        ]);
    }

    public function create()
    {
        return Inertia::render('SocialAccounts/Create', [
            'platforms' => Platform::where('is_active', true)->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        SocialAccount::create($validated);

        return redirect()->route('social-accounts.index')
            ->with('message', 'Akun berhasil ditambahkan');
    }

    public function edit(SocialAccount $socialAccount)
    {
        return Inertia::render('SocialAccounts/Edit', [
            'account' => $socialAccount,
            'platforms' => Platform::where('is_active', true)->get()
        ]);
    }

    public function update(Request $request, SocialAccount $socialAccount)
    {
        $validated = $request->validate([
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $socialAccount->update($validated);

        return redirect()->route('social-accounts.index')
            ->with('message', 'Akun berhasil diperbarui');
    }

    public function destroy(SocialAccount $socialAccount)
    {
        $socialAccount->delete();

        return redirect()->route('social-accounts.index')
            ->with('message', 'Akun berhasil dihapus');
    }

    public function toggleStatus(SocialAccount $socialAccount)
    {
        $socialAccount->update([
            'is_active' => !$socialAccount->is_active
        ]);

        return redirect()->back()
            ->with('message', 'Status akun berhasil diperbarui');
    }
} 
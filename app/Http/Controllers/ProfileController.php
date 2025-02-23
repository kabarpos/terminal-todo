<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Validate file
                $request->validate([
                    'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);

                $avatar = $request->file('avatar');
                
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Generate unique filename
                $filename = 'avatar-' . time() . '.' . $avatar->getClientOriginalExtension();
                
                // Store new avatar
                $path = $avatar->storeAs('avatars', $filename, 'public');
                
                if (!$path) {
                    throw new \Exception('Gagal menyimpan avatar');
                }
                
                $data['avatar'] = $path;
            }

            $user->fill($data);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return Redirect::route('profile.edit')
                ->with('message', 'Profile berhasil diperbarui.');
                
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->withErrors(['avatar' => 'Gagal mengupload avatar: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

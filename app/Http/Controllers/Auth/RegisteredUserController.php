<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'phone' => ['required', 'string', 'regex:/^08[0-9]{8,11}$/', 'min:10', 'max:13'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex' => 'Format WhatsApp tidak valid. Gunakan format Indonesia (contoh: 081234567890)',
            'phone.min' => 'Nomor WhatsApp minimal 10 digit',
            'phone.max' => 'Nomor WhatsApp maksimal 13 digit',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'status_reason' => 'Menunggu persetujuan admin',
        ]);

        // Assign default 'user' role
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->assignRole($userRole);
        }

        event(new Registered($user));

        return redirect()->route('login')
            ->with('status', 'Registrasi berhasil! Silakan tunggu persetujuan admin untuk dapat login ke sistem.');
    }
}

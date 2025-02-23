<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Cek user berdasarkan email
        $user = User::where('email', $this->email)->first();

        // Jika user ditemukan, cek statusnya
        if ($user) {
            if ($user->status !== 'active') {
                RateLimiter::hit($this->throttleKey());
                
                $message = match($user->status) {
                    'pending' => 'Akun Anda masih menunggu persetujuan admin. Silakan coba lagi nanti.',
                    'rejected' => 'Akun Anda ditolak. Alasan: ' . ($user->status_reason ?: 'Tidak ada alasan yang diberikan.'),
                    'banned' => 'Akun Anda telah dibanned. Alasan: ' . ($user->status_reason ?: 'Tidak ada alasan yang diberikan.'),
                    'inactive' => 'Akun Anda sedang tidak aktif. Silakan hubungi admin.',
                    default => 'Akun Anda tidak dapat mengakses sistem saat ini.'
                };

                throw ValidationException::withMessages([
                    'email' => $message,
                ]);
            }
        }

        // Coba login hanya jika user active
        if (!Auth::attempt(array_merge(
            $this->only('email', 'password'),
            ['status' => 'active']
        ), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}

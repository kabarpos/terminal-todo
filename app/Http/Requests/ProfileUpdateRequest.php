<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['required', 'string', 'max:15', 'regex:/^08[0-9]{9,11}$/'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format: 08xx-xxxx-xxxx',
            'avatar.image' => 'File harus berupa gambar (JPG, PNG, GIF)',
            'avatar.max' => 'Ukuran gambar tidak boleh lebih dari 2MB',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Enums\RolePengguna;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User|null $targetUser */
        $targetUser = $this->route('user');
        $userId = $targetUser instanceof User ? $targetUser->id_user : $targetUser;

        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id_user'),
            ],
            'role' => ['required', new Enum(RolePengguna::class)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Configure additional validation checks.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            /** @var User|null $targetUser */
            $targetUser = $this->route('user');
            if (! $targetUser instanceof User) {
                $targetUser = User::find($targetUser);
            }

            if ($targetUser && $targetUser->isAdmin() && $this->input('role') !== 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    $validator->errors()->add('role', 'Tidak dapat menurunkan peran Administrator terakhir dalam sistem.');
                }
            }
        });
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar oleh pengguna lain.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.enum' => 'Peran pengguna yang dipilih tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}

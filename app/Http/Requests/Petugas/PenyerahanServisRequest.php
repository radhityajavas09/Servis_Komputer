<?php

namespace App\Http\Requests\Petugas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PenyerahanServisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isPetugas() || $this->user()?->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pembayaran_lunas' => ['required', 'accepted'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pembayaran_lunas.required' => 'Konfirmasi pelunasan pembayaran wajib dicentang.',
            'pembayaran_lunas.accepted' => 'Anda wajib memastikan bahwa pembayaran servis telah lunas sebelum menyerahkan perangkat.',
        ];
    }
}

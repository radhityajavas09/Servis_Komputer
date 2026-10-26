<?php

namespace App\Http\Requests\Petugas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePendaftaranServisRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:100'],
            'no_telepon' => [
                'required',
                'string',
                'min:9',
                'max:20',
                'regex:/^[0-9+\-\s]+$/',
            ],
            'alamat' => ['required', 'string', 'max:500'],
            'jenis_perangkat' => ['required', 'string', 'max:50'],
            'merk_tipe' => ['required', 'string', 'max:100'],
            'keluhan' => ['required', 'string', 'max:1000'],
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
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'nama.max' => 'Nama pelanggan maksimal 100 karakter.',
            'no_telepon.required' => 'Nomor telepon pelanggan wajib diisi.',
            'no_telepon.min' => 'Nomor telepon minimal 9 digit.',
            'no_telepon.max' => 'Nomor telepon maksimal 20 digit.',
            'no_telepon.regex' => 'Format nomor telepon tidak valid.',
            'alamat.required' => 'Alamat pelanggan wajib diisi.',
            'alamat.max' => 'Alamat pelanggan maksimal 500 karakter.',
            'jenis_perangkat.required' => 'Jenis perangkat wajib dipilih.',
            'jenis_perangkat.max' => 'Jenis perangkat maksimal 50 karakter.',
            'merk_tipe.required' => 'Merk dan tipe perangkat wajib diisi.',
            'merk_tipe.max' => 'Merk dan tipe perangkat maksimal 100 karakter.',
            'keluhan.required' => 'Keluhan kerusakan wajib diisi.',
            'keluhan.max' => 'Keluhan kerusakan maksimal 1000 karakter.',
        ];
    }
}

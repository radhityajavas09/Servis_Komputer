<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSparepartRequest extends FormRequest
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
        return [
            'nama_sparepart' => ['required', 'string', 'max:100', 'unique:sparepart,nama_sparepart'],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
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
            'nama_sparepart.required' => 'Nama sparepart wajib diisi.',
            'nama_sparepart.max' => 'Nama sparepart maksimal 100 karakter.',
            'nama_sparepart.unique' => 'Nama sparepart sudah terdaftar dalam sistem.',
            'harga.required' => 'Harga sparepart wajib diisi.',
            'harga.integer' => 'Harga sparepart harus berupa angka bulat.',
            'harga.min' => 'Harga sparepart tidak boleh kurang dari 0.',
            'stok.required' => 'Jumlah stok awal wajib diisi.',
            'stok.integer' => 'Jumlah stok harus berupa angka bulat.',
            'stok.min' => 'Jumlah stok tidak boleh kurang dari 0.',
        ];
    }
}

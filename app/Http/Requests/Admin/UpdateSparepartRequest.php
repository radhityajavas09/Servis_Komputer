<?php

namespace App\Http\Requests\Admin;

use App\Models\Sparepart;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSparepartRequest extends FormRequest
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
        /** @var Sparepart|null $sparepart */
        $sparepart = $this->route('sparepart');
        $sparepartId = $sparepart instanceof Sparepart ? $sparepart->id_sparepart : $sparepart;

        return [
            'nama_sparepart' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sparepart', 'nama_sparepart')->ignore($sparepartId, 'id_sparepart'),
            ],
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
            'stok.required' => 'Jumlah stok wajib diisi.',
            'stok.integer' => 'Jumlah stok harus berupa angka bulat.',
            'stok.min' => 'Jumlah stok tidak boleh kurang dari 0.',
        ];
    }
}

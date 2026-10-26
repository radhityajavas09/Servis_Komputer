<?php

namespace App\Http\Requests\Teknisi;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDiagnosisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isTeknisi() || $this->user()?->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'diagnosis' => ['nullable', 'string', 'max:2000'],
            'tindakan' => ['nullable', 'string', 'max:2000'],
            'biaya_jasa' => ['required', 'integer', 'min:0'],
            'spareparts' => ['nullable', 'array'],
            'spareparts.*.id_sparepart' => ['required_with:spareparts', 'integer', 'exists:sparepart,id_sparepart'],
            'spareparts.*.jumlah' => ['required_with:spareparts', 'integer', 'min:1'],
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
            'biaya_jasa.required' => 'Biaya jasa wajib diisi (minimal 0 jika gratis).',
            'biaya_jasa.integer' => 'Biaya jasa harus berupa bilangan bulat.',
            'biaya_jasa.min' => 'Biaya jasa tidak boleh kurang dari 0.',
            'spareparts.*.id_sparepart.required_with' => 'Pilihan suku cadang/sparepart wajib dipilih.',
            'spareparts.*.id_sparepart.exists' => 'Sparepart yang dipilih tidak ditemukan dalam katalog.',
            'spareparts.*.jumlah.required_with' => 'Jumlah sparepart wajib diisi.',
            'spareparts.*.jumlah.min' => 'Jumlah sparepart minimal 1 unit.',
        ];
    }
}

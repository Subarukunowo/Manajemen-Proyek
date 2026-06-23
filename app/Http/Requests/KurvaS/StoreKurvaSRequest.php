<?php

namespace App\Http\Requests\KurvaS;

use Illuminate\Foundation\Http\FormRequest;

class StoreKurvaSRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'periode'             => ['required', 'date'],
            'rencana_kumulatif'   => ['required', 'numeric', 'min:0', 'max:100'],
            'realisasi_kumulatif' => ['required', 'numeric', 'min:0', 'max:100'],
            'rencana_periode'     => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'realisasi_periode'   => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'rencana_kumulatif.max'   => 'Rencana kumulatif tidak boleh melebihi 100%.',
            'realisasi_kumulatif.max' => 'Realisasi kumulatif tidak boleh melebihi 100%.',
            'periode.required'        => 'Periode wajib diisi.',
        ];
    }
}
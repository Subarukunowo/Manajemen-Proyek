<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori'   => ['required', 'string', 'max:100'],
            'anggaran'   => ['required', 'numeric', 'min:0'],
            'realisasi'  => ['sometimes', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'anggaran.min'  => 'Anggaran tidak boleh bernilai negatif.',
            'realisasi.min' => 'Realisasi tidak boleh bernilai negatif.',
        ];
    }
}
<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori'   => ['sometimes', 'string', 'max:100'],
            'anggaran'   => ['sometimes', 'numeric', 'min:0'],
            'realisasi'  => ['sometimes', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
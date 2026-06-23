<?php

namespace App\Http\Requests\Risk;

use Illuminate\Foundation\Http\FormRequest;

class StoreRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deskripsi_risiko' => ['required', 'string'],
            'probabilitas'     => ['required', 'in:Low,Medium,High'],
            'dampak'           => ['required', 'in:Low,Medium,High'],
            'mitigasi'         => ['nullable', 'string'],
            'status'           => ['sometimes', 'in:Identified,Mitigated,Occurred,Closed'],
            'assigned_to'      => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.exists' => 'User yang ditugaskan tidak ditemukan.',
        ];
    }
}
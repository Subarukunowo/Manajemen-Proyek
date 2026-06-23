<?php

namespace App\Http\Requests\Risk;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deskripsi_risiko' => ['sometimes', 'string'],
            'probabilitas'     => ['sometimes', 'in:Low,Medium,High'],
            'dampak'           => ['sometimes', 'in:Low,Medium,High'],
            'mitigasi'         => ['nullable', 'string'],
            'status'           => ['sometimes', 'in:Identified,Mitigated,Occurred,Closed'],
            'assigned_to'      => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
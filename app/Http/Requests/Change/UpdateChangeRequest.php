<?php

namespace App\Http\Requests\Change;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'           => ['sometimes', 'string', 'max:255'],
            'deskripsi'       => ['sometimes', 'string'],
            'dampak'          => ['sometimes', 'in:Low,Medium,High'],
            'biaya_perubahan' => ['sometimes', 'numeric'],
        ];
    }
}
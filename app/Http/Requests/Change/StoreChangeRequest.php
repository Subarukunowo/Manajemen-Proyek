<?php

namespace App\Http\Requests\Change;

use Illuminate\Foundation\Http\FormRequest;

class StoreChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'           => ['required', 'string', 'max:255'],
            'deskripsi'       => ['required', 'string'],
            'dampak'          => ['required', 'in:Low,Medium,High'],
            'biaya_perubahan' => ['sometimes', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required'     => 'Judul change request wajib diisi.',
            'deskripsi.required' => 'Deskripsi perubahan wajib diisi.',
            'dampak.in'          => 'Dampak harus salah satu dari: Low, Medium, High.',
        ];
    }
}
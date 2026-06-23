<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'persen_selesai' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'persen_selesai.required' => 'Persentase selesai wajib diisi.',
            'persen_selesai.min'      => 'Persentase selesai minimal 0.',
            'persen_selesai.max'      => 'Persentase selesai maksimal 100.',
        ];
    }
}
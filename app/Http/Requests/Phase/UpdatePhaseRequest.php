<?php

namespace App\Http\Requests\Phase;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'            => ['sometimes', 'string', 'max:255'],
            'tanggal_mulai'   => ['sometimes', 'date'],
            'tanggal_selesai' => ['sometimes', 'date', 'after_or_equal:tanggal_mulai'],
            'status'          => ['sometimes', 'in:Pending,On_Progress,Completed'],
            'urutan'          => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
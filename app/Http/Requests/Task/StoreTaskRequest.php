<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id'        => ['required', 'integer', 'exists:project_phases,id'],
            'parent_task_id'  => ['nullable', 'integer', 'exists:project_tasks,id'],
            'nama'            => ['required', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'durasi_hari'     => ['sometimes', 'integer', 'min:1'],
            'persen_selesai'  => ['sometimes', 'integer', 'min:0', 'max:100'],
            'status'          => ['sometimes', 'in:Todo,In_Progress,Blocked,Done'],
            'prioritas'       => ['sometimes', 'in:Low,Medium,High,Critical'],
            'assigned_to'     => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'phase_id.exists'       => 'Phase yang dipilih tidak ditemukan.',
            'parent_task_id.exists' => 'Parent task tidak ditemukan.',
            'assigned_to.exists'    => 'User yang ditugaskan tidak ditemukan.',
            'persen_selesai.max'    => 'Persentase selesai maksimal 100.',
        ];
    }
}
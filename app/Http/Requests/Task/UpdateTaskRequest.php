<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id'        => ['sometimes', 'integer', 'exists:project_phases,id'],
            'parent_task_id'  => ['nullable', 'integer', 'exists:project_tasks,id'],
            'nama'            => ['sometimes', 'string', 'max:255'],
            'deskripsi'       => ['nullable', 'string'],
            'tanggal_mulai'   => ['sometimes', 'date'],
            'tanggal_selesai' => ['sometimes', 'date', 'after_or_equal:tanggal_mulai'],
            'durasi_hari'     => ['sometimes', 'integer', 'min:1'],
            'persen_selesai'  => ['sometimes', 'integer', 'min:0', 'max:100'],
            'status'          => ['sometimes', 'in:Todo,In_Progress,Blocked,Done'],
            'prioritas'       => ['sometimes', 'in:Low,Medium,High,Critical'],
            'assigned_to'     => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
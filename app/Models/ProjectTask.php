<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int             $id
 * @property int             $project_id
 * @property int|null        $phase_id
 * @property int|null        $parent_task_id
 * @property string          $nama
 * @property string|null     $deskripsi
 * @property \Carbon\Carbon|null $tanggal_mulai
 * @property \Carbon\Carbon|null $tanggal_selesai
 * @property int|null        $durasi_hari
 * @property int             $persen_selesai
 * @property string          $status
 * @property string          $prioritas
 * @property int|null        $assigned_to
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 */
class ProjectTask extends Model
{
    protected $table = 'project_tasks';
    protected $fillable = [
        'project_id', 'phase_id', 'parent_task_id',
        'nama', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai',
        'durasi_hari', 'persen_selesai', 'status', 'prioritas', 'assigned_to',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'persen_selesai'  => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProjectTask $task) {
            // Clamp progress
            $task->persen_selesai = min(100, max(0, (int) $task->persen_selesai));

            // Auto-calc durasi_hari dari tanggal jika keduanya ada
            if ($task->tanggal_mulai && $task->tanggal_selesai) {
                $task->durasi_hari = (int) $task->tanggal_mulai->diffInDays($task->tanggal_selesai);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'phase_id');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'parent_task_id');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'parent_task_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(ProjectResource::class, 'task_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int             $id
 * @property int             $project_id
 * @property string          $nama
 * @property int             $urutan
 * @property \Carbon\Carbon|null $tanggal_mulai
 * @property \Carbon\Carbon|null $tanggal_selesai
 * @property string          $status
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 */
class ProjectPhase extends Model
{
    protected $table = 'project_phases';
    protected $fillable = [
        'project_id', 'nama', 'urutan',
        'tanggal_mulai', 'tanggal_selesai', 'status',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'phase_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $project_id
 * @property string      $nama
 * @property \Carbon\Carbon $tanggal_target
 * @property \Carbon\Carbon|null $tanggal_aktual
 * @property string      $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProjectMilestone extends Model
{
    protected $table = 'project_milestones';
    protected $fillable = [
        'project_id', 'nama', 'tanggal_target', 'tanggal_aktual', 'status',
    ];

    protected $casts = [
        'tanggal_target' => 'date',
        'tanggal_aktual' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getIsDelayedAttribute(): bool
    {
        if ($this->status === 'Achieved' && $this->tanggal_aktual && $this->tanggal_target) {
            return $this->tanggal_aktual->gt($this->tanggal_target);
        }

        return $this->status === 'Delayed';
    }
}
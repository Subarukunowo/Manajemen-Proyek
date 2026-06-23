<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $project_id
 * @property \Carbon\Carbon $periode
 * @property float       $rencana_kumulatif
 * @property float       $realisasi_kumulatif
 * @property float       $rencana_periode
 * @property float       $realisasi_periode
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProjectKurvaS extends Model
{
    protected $table = 'project_kurva_s';

    protected $fillable = [
        'project_id', 'periode',
        'rencana_kumulatif', 'realisasi_kumulatif',
        'rencana_periode', 'realisasi_periode',
    ];

    protected $casts = [
        'periode'             => 'date',
        'rencana_kumulatif'   => 'decimal:2',
        'realisasi_kumulatif' => 'decimal:2',
        'rencana_periode'     => 'decimal:2',
        'realisasi_periode'   => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getDeviasiAttribute(): float
    {
        return round((float) $this->realisasi_kumulatif - (float) $this->rencana_kumulatif, 2);
    }
}
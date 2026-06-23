<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $project_id
 * @property string      $kategori
 * @property float       $anggaran
 * @property float       $realisasi
 * @property string|null $keterangan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProjectBudget extends Model
{
    protected $table = 'project_budget';
    protected $fillable = [
        'project_id', 'kategori', 'anggaran', 'realisasi', 'keterangan',
    ];

    protected $casts = [
        'anggaran'  => 'decimal:2',
        'realisasi' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getVariansiAttribute(): float
    {
        return (float) $this->anggaran - (float) $this->realisasi;
    }
}
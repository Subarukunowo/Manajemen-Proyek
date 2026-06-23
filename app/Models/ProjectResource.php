<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $project_id
 * @property int|null    $task_id
 * @property string      $nama_resource
 * @property string      $tipe
 * @property float       $jumlah
 * @property string      $satuan
 * @property float       $biaya_satuan
 * @property float       $total_biaya
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProjectResource extends Model
{
    protected $table = 'project_resources';
    protected $fillable = [
        'project_id', 'task_id', 'nama_resource',
        'tipe', 'jumlah', 'satuan', 'biaya_satuan', 'total_biaya',
    ];

    protected $casts = [
        'jumlah'      => 'decimal:2',
        'biaya_satuan' => 'decimal:2',
        'total_biaya'  => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProjectResource $resource) {
            $resource->total_biaya = $resource->jumlah * $resource->biaya_satuan;
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }
}
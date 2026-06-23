<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $project_id
 * @property string      $deskripsi_risiko
 * @property string      $probabilitas
 * @property string      $dampak
 * @property int         $skor_risiko
 * @property string|null $mitigasi
 * @property string      $status
 * @property int|null    $assigned_to
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProjectRisk extends Model
{
    protected $table = 'project_risks';
    protected $fillable = [
        'project_id', 'deskripsi_risiko', 'probabilitas',
        'dampak', 'skor_risiko', 'mitigasi', 'status', 'assigned_to',
    ];

    protected $casts = [
        'skor_risiko' => 'integer',
    ];

    private const SKOR_MAP = ['Low' => 1, 'Medium' => 2, 'High' => 3];

    protected static function booted(): void
    {
        static::saving(function (ProjectRisk $risk) {
            $p = self::SKOR_MAP[$risk->probabilitas] ?? 1;
            $d = self::SKOR_MAP[$risk->dampak] ?? 1;
            $risk->skor_risiko = $p * $d;
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
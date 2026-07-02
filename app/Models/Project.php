<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int             $id
 * @property string          $kode
 * @property string          $nama
 * @property string|null     $deskripsi
 * @property string          $status
 * @property \Carbon\Carbon  $tanggal_mulai
 * @property \Carbon\Carbon  $tanggal_selesai
 * @property float           $anggaran
 * @property int             $created_by
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 */
class Project extends Model
{
    protected $fillable = [
        'kode', 'nama', 'deskripsi', 'status',
        'tanggal_mulai', 'tanggal_selesai', 'anggaran', 'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'anggaran'        => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('urutan');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(ProjectResource::class);
    }

    public function budgetBreakdowns(): HasMany
    {
        return $this->hasMany(ProjectBudget::class);
    }

    public function risks(): HasMany
    {
        return $this->hasMany(ProjectRisk::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(ProjectChange::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function outsourcings(): HasMany
    {
        return $this->hasMany(ProjectOutsourcing::class);
    }

    public function sCurveRecords(): HasMany
    {
        return $this->hasMany(ProjectKurvaS::class)->orderBy('periode');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(ProjectRole::class)->orderBy('nama');
    }

    /**
     * Total anggaran dari rincian budget (lebih akurat dari projects.anggaran).
     * Gunakan ini untuk tampilan, projects.anggaran hanya sebagai plafon awal.
     */
    public function getAnggaranBudgetAttribute(): float
    {
        return (float) $this->budgetBreakdowns()->sum('anggaran');
    }

    public function getRealisasiBudgetAttribute(): float
    {
        return (float) $this->budgetBreakdowns()->sum('realisasi');
    }
}
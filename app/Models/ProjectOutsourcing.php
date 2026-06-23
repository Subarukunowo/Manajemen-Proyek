<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $project_id
 * @property string      $nama_vendor
 * @property string|null $lingkup_kerja
 * @property float       $nilai_kontrak
 * @property \Carbon\Carbon $tanggal_mulai
 * @property \Carbon\Carbon $tanggal_selesai
 * @property string      $status
 * @property int         $persen_selesai
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProjectOutsourcing extends Model
{
    protected $table = 'project_outsourcing';
    protected $fillable = [
        'project_id', 'nama_vendor', 'lingkup_kerja',
        'nilai_kontrak', 'tanggal_mulai', 'tanggal_selesai',
        'status', 'persen_selesai',
    ];

    protected $casts = [
        'nilai_kontrak'   => 'decimal:2',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'persen_selesai'  => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
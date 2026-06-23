<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int             $id
 * @property int             $project_id
 * @property string          $nomor_cr
 * @property string          $judul
 * @property string|null     $deskripsi
 * @property string          $status
 * @property string|null     $dampak
 * @property float|null      $biaya_perubahan
 * @property int             $requested_by
 * @property int|null        $approved_by
 * @property \Carbon\Carbon  $tanggal_request
 * @property \Carbon\Carbon|null $tanggal_approval
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 */
class ProjectChange extends Model
{
    protected $table = 'project_changes';
    protected $fillable = [
        'project_id', 'nomor_cr', 'judul', 'deskripsi',
        'status', 'dampak', 'biaya_perubahan',
        'requested_by', 'approved_by', 'tanggal_request', 'tanggal_approval',
    ];

    protected $casts = [
        'biaya_perubahan'  => 'decimal:2',
        'tanggal_request'  => 'date',
        'tanggal_approval' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $project_id
 * @property string      $nama
 * @property string      $warna
 * @property string|null $deskripsi
 * @property bool        $is_default
 */
class ProjectRole extends Model
{
    protected $table    = 'project_roles';
    protected $fillable = ['project_id', 'nama', 'warna', 'deskripsi', 'is_default'];
    protected $casts    = ['is_default' => 'boolean'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Warna kontras untuk teks berdasarkan latar
    public function getTextColorAttribute(): string
    {
        $hex = ltrim($this->warna, '#');
        [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
}

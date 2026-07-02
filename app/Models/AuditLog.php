<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int|null    $user_id
 * @property string|null $user_email
 * @property string      $action
 * @property string|null $model
 * @property int|null    $model_id
 * @property string|null $route
 * @property string|null $method
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null  $old_values
 * @property array|null  $new_values
 * @property string|null $description
 */
class AuditLog extends Model
{
    protected $fillable = [
        'user_id','user_email','action','model','model_id',
        'route','method','ip_address','user_agent',
        'old_values','new_values','description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Static helper ─────────────────────────────────────────────
    public static function record(string $action, ?string $description = null, array $extra = []): void
    {
        try {
            static::create(array_merge([
                'user_id'    => auth()->id(),
                'user_email' => auth()->user()?->email,
                'action'     => $action,
                'route'      => request()->path(),
                'method'     => request()->method(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description'=> $description,
            ], $extra));
        } catch (\Exception $e) {
            // Audit gagal tidak boleh merusak request utama
            \Illuminate\Support\Facades\Log::warning('AuditLog::record failed: ' . $e->getMessage());
        }
    }
}

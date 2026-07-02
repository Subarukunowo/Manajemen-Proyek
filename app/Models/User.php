<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string|null $google_id
 * @property string|null $avatar
 * @property string      $role        admin | user
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'email', 'password', 'google_id', 'avatar', 'role'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    // ── Role helpers ─────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // ── Relationships ─────────────────────────────────────────────
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'assigned_to');
    }

    public function assignedRisks(): HasMany
    {
        return $this->hasMany(ProjectRisk::class, 'assigned_to');
    }

    public function requestedChanges(): HasMany
    {
        return $this->hasMany(ProjectChange::class, 'requested_by');
    }
}

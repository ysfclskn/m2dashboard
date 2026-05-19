<?php

namespace App\Models;

use App\Enums\ProjectRole;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id', 'name', 'slug', 'description', 'genre', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class)->latest();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function wikiPages(): HasMany
    {
        return $this->hasMany(WikiPage::class)->orderBy('title');
    }

    public function progressReports(): HasMany
    {
        return $this->hasMany(ProgressReport::class)->latest();
    }

    public function activeSprint(): ?Sprint
    {
        return $this->sprints()->where('status', 'active')->first();
    }

    public function memberRole(User $user): ?ProjectRole
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member?->role;
    }

    public function hasMember(User $user): bool
    {
        return $this->owner_id === $user->id
            || $this->members()->where('user_id', $user->id)->exists();
    }

    public function userCan(User $user, string $ability): bool
    {
        if ($this->owner_id === $user->id) return true;

        $role = $this->memberRole($user);
        if (!$role) return false;

        return match($ability) {
            'manageProject' => $role->canManageProject(),
            'manageTasks'   => $role->canManageTasks(),
            'manageWiki'    => $role->canManageWiki(),
            'manageMembers' => $role->canManageMembers(),
            default         => false,
        };
    }
}

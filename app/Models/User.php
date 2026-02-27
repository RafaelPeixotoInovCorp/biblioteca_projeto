<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships for roles and permissions
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')
            ->via('roles');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission): bool
    {
        if (is_string($permission)) {
            return $this->roles->flatMap->permissions->contains('name', $permission);
        }
        return !!$permission->intersect($this->roles->flatMap->permissions)->count();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is content manager
     */
    public function isGestorConteudo(): bool
    {
        return $this->hasRole('gestor_conteudo');
    }

    /**
     * Check if user is client
     */
    public function isCliente(): bool
    {
        return $this->hasRole('cliente');
    }

    /**
     * Check if user can manage books (admin or content manager)
     */
    public function canManageBooks(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('gestor_conteudo');
    }

    /**
     * Check if user can manage authors (admin or content manager)
     */
    public function canManageAuthors(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('gestor_conteudo');
    }

    /**
     * Check if user can manage publishers (admin or content manager)
     */
    public function canManagePublishers(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('gestor_conteudo');
    }

    /**
     * Check if user can view books (everyone with role)
     */
    public function canViewBooks(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('gestor_conteudo') || $this->hasRole('cliente');
    }

    /**
     * Check if user can view authors (everyone with role)
     */
    public function canViewAuthors(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('gestor_conteudo') || $this->hasRole('cliente');
    }

    /**
     * Check if user can view publishers (everyone with role)
     */
    public function canViewPublishers(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('gestor_conteudo') || $this->hasRole('cliente');
    }

    /**
     * Check if user has any view permission
     */
    public function hasAnyViewPermission(): bool
    {
        return $this->canViewBooks() || $this->canViewAuthors() || $this->canViewPublishers();
    }
}

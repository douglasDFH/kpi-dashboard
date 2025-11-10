<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'phone',
        'position',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Relación con rol
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación con logs de auditoría
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Relación many-to-many con permisos personalizados
     */
    public function customPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
                    ->withTimestamps();
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     * Si tiene permisos personalizados configurados, SOLO usa esos (ignora el rol)
     * Si NO tiene permisos personalizados, usa los permisos del rol
     */
    public function hasPermission(string $permissionName): bool
    {
        // Verificar si el usuario tiene al menos un permiso personalizado configurado
        $hasCustomPermissions = $this->customPermissions()->exists();
        
        if ($hasCustomPermissions) {
            // Si tiene permisos personalizados, SOLO usar esos
            return $this->customPermissions()
                ->where('name', $permissionName)
                ->exists();
        }

        // Si no tiene permisos personalizados, usar los permisos del rol
        return $this->role && $this->role->hasPermission($permissionName);
    }

    /**
     * Verificar si el usuario puede realizar una acción (alias de hasPermission)
     */
    public function can($ability, $arguments = []): bool
    {
        // Si es una habilidad del sistema de autorización de Laravel, usar parent
        if (is_string($ability) && str_contains($ability, '.')) {
            return $this->hasPermission($ability);
        }
        
        return parent::can($ability, $arguments);
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Verificar si el usuario es SuperAdmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Verificar si el usuario es Admin o SuperAdmin
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['superadmin', 'admin']);
    }

    /**
     * Actualizar último login
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Obtener el nombre del rol
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->display_name : 'Sin rol';
    }

    /**
     * Obtener el color del badge del rol
     */
    public function getRoleColorAttribute(): string
    {
        if (!$this->role) {
            return 'gray';
        }

        return match($this->role->name) {
            'superadmin' => 'red',
            'admin' => 'orange',
            'gerente' => 'blue',
            'supervisor' => 'green',
            'operador' => 'purple',
            'calidad' => 'yellow',
            'mantenimiento' => 'gray',
            default => 'gray',
        };
    }
}

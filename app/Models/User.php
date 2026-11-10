<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_STUDENT = 'student';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_STUDENT => 'Student',
        self::ROLE_MANAGER => 'Hostel Manager',
        self::ROLE_ADMIN => 'Admin',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function canManageHostel(Hostel $hostel): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->isManager() && $this->managedHostels()->whereKey($hostel->getKey())->exists();
    }

    public function managedHostels()
    {
        return $this->belongsToMany(Hostel::class, 'hostel_user')->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
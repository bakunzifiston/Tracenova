<?php

namespace App\Models;

use App\Models\App;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'is_approved',
        'is_super_admin',
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
            'is_approved' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /** Users can access the app (dashboard, reports) only when approved or when they are super admin. */
    public function canAccessApp(): bool
    {
        return $this->isSuperAdmin() || $this->isApproved();
    }

    public function apps(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(App::class, 'user_id');
    }
}

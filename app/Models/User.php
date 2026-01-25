<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{

    public const ROLE_SUPER_ADMIN = 1;
    public const ROLE_MONITOR = 2;
    public const ROLE_SUPERVISOR = 3;
    public const ROLE_PARENT = 4;

    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image_path',
        'image',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canImpersonate(): bool
    {
        // Only you (ID 2) can impersonate others
        return $this->id === 2;
    }

    /**
     * Define who can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        // Prevent anyone from impersonating Super Admins for safety
        return (int) $this->role !== self::ROLE_SUPER_ADMIN;
    }

    public function subscriber()
    {
        return $this->hasOne(Subscriber::class);
    }

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
        'role' => 'integer',
        'status' => 'integer',
    ];
}


    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }
    // app/Models/User.php

    public function supervisorActivities()
    {
        return $this->hasMany(SupervisorActivityDetail::class, 'supervisor_id');
    }



public function followUpPeriods(): HasMany
{
    return $this->hasMany(FollowUpPeriod::class);
}



public function isSuperAdmin(): bool { return (int) $this->role === self::ROLE_SUPER_ADMIN; }
public function isAdmin(): bool      { return (int) $this->role === self::ROLE_MONITOR; }
public function isSupervisor(): bool { return (int) $this->role === self::ROLE_SUPERVISOR; }
public function isMember(): bool     { return (int) $this->role === self::ROLE_PARENT; }




public function isStaff(): bool
{
    return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_MONITOR, self::ROLE_SUPERVISOR], true);
}

public function subscribers()
{
    return $this->hasMany(Subscriber::class);
}


}

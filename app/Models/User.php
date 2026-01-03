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
    public const ROLE_ADMIN = 2;
    public const ROLE_SUPERVISOR = 3;
    public const ROLE_MEMBER = 4;

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
        'image',
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



public function isSuperAdmin(): bool { return $this->role === self::ROLE_SUPER_ADMIN; }
public function isAdmin(): bool      { return $this->role === self::ROLE_ADMIN; }
public function isSupervisor(): bool { return $this->role === self::ROLE_SUPERVISOR; }
public function isMember(): bool     { return $this->role === self::ROLE_MEMBER; }

public function isStaff(): bool
{
    return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_SUPERVISOR], true);
}


}

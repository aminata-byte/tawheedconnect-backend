<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'photo',
        'bio',
        'city',
        'country',
        'role',
        'is_active',
        'phone_verified',
        'verification_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function association()
    {
        return $this->hasOne(Association::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'oustaze_id');
    }

    public function eventParticipations()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function followedAssociations()
    {
        return $this->belongsToMany(Association::class, 'followers');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Helpers
    public function isMember()
    {
        return $this->role === 'member';
    }

    public function isAssociation()
    {
        return $this->role === 'association';
    }

    public function isOustaze()
    {
        return $this->role === 'oustaze';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}

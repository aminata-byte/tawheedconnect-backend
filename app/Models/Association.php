<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Association extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo',
        'cover_image',
        'address',
        'city',
        'region',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'social_links',
        'category',
        'metadata',
        'followers_count',
        'events_count',
        'views_count',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'metadata' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers');
    }

    public function audios()
    {
        return $this->hasMany(Audio::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}

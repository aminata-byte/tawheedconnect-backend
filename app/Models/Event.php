<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'association_id',
        'title',
        'description',
        'image',
        'location',
        'city',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'type',
        'category',
        'max_participants',
        'participants_count',
        'requires_registration',
        'status',
        'organizers',
        'tags',
        'views_count',
        'shares_count',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'requires_registration' => 'boolean',
        'organizers' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot('status', 'registered_at', 'has_attended', 'attended_at', 'notes')
            ->withTimestamps();
    }
}

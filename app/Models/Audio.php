<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'association_id',
        'oustaze_id',
        'title',
        'description',
        'audio_url',
        'thumbnail',
        'duration',
        'category',
        'tags',
        'plays_count',
        'downloads_count',
        'likes_count',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function oustaze()
    {
        return $this->belongsTo(User::class, 'oustaze_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'association_id',
        'oustaze_id',
        'title',
        'description',
        'video_url',
        'thumbnail',
        'duration',
        'category',
        'tags',
        'views_count',
        'likes_count',
        'shares_count',
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

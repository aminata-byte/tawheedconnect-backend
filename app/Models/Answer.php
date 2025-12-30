<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'oustaze_id',
        'answer',
        'audio_url',
        'video_url',
        'references',
    ];

    protected $casts = [
        'references' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function oustaze()
    {
        return $this->belongsTo(User::class, 'oustaze_id');
    }
}

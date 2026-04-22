<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TopicRating extends Model
{
    use HasFactory;
    protected $fillable = [
    'teacher_id',
    'topic_id',
    'rating',
    'label',
    'classroom_id'
];

public function classroom()
{
    return $this->belongsTo(\App\Models\Classroom::class);
}

public function topic()
{
    return $this->belongsTo(\App\Models\Topic::class);
}

public function teacher()
{
    return $this->belongsTo(\App\Models\Teacher::class);
}


}

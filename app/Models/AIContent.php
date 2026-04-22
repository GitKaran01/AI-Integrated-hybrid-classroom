<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AIContent extends Model
{
    use HasFactory;

    protected $table = 'ai_contents'; // ✅ FIX

protected $fillable = [
    'teacher_id',
    'classroom_id',
    'topic_id',
    'questions',
    'answers',
    'pdf_url'
];
}
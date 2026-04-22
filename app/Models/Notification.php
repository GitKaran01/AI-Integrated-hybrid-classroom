<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    // In columns ko mass-assignment ke liye kholna bahut zaruri hai
    protected $fillable = [
        'teacher_id', 
        'classroom_id', 
        'topic_id', 
        'title', 
        'message', 
        'pdf_url',    // <--- Ensure this is exactly like this
        'type', 
        'is_done'
    ];

    public $timestamps = true;
}
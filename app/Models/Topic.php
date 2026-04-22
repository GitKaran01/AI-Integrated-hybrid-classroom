<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;

protected $fillable = [
    'name',
    'subject_id',
    'classroom_id'
];


public $timestamps = true; // 🔥 IMPORTANT



public function classroom()
{
    return $this->belongsTo(\App\Models\Classroom::class);
}


}


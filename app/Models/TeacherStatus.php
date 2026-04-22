<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TeacherStatus extends Model
{
    use HasFactory;
  protected $fillable = ['teacher_id', 'status', 'date'];
}

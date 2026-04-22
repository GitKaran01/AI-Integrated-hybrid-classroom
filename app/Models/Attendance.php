<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'attendances';

    // Columns that can be mass-assigned
    protected $fillable = [
        'student_id',
        'classroom_id',
        'date',
        'status',
        'confidence'
    ];

    /**
     * ✅ NEW: Relationship to fetch student name dynamically!
     * This links the attendance 'student_id' to the Student 'id'.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
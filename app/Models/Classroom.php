<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * ✅ IS FUNCTION KI WAJAH SE ERROR AA RAHA HAI.
     * Yeh batata hai ki Classroom ke andar bahut saare Students hain.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'classroom_id', 'id');
    }

    /**
     * Attendance ke liye bhi relationship add kar dete hain safe side ke liye.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'classroom_id', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Password ko hidden rakhne ke liye taaki JSON me na dikhe
    protected $hidden = [
        'password',
    ];
}
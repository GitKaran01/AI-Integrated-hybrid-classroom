<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRevisionMaterial extends Model
{
    protected $fillable = ['classroom_id', 'topic_name', 'pdf_path'];
}

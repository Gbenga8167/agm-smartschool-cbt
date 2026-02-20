<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
     protected $fillable = [
        'name',
        'is_current',
        'test_limit', 
    
    ];
}

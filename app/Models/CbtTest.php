<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtTest extends Model
{
    protected $fillable = [
        'student_classes_id',
        'subject_id',
        'teacher_id',
        'title',
        'term',
        'session',
        'assessment_type',
        'duration_minutes',
        'start_time',
        'end_time',     		
    ];

    //A test has many questions
    public function questions(){
        return $this->hasMany(CbtQuestion::class, 'cbt_test_id');
    }

    //A test has many student attempts
    public function attempts(){
        return $this->hasMany(CbtAttempt::class, 'cbt_test_id');
    }

    //Link to subject
    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    //Link to class
    public function class(){
        return $this->belongsTo(StudentClasses::class, 'student_classes_id');
    }

    
    //Link to teacher
    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
//
}

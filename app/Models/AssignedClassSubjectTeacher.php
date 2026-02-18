<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedClassSubjectTeacher extends Model
{
        protected $fillable = [
        'teacher_id',
        'student_classes_id', 
        'subject_id',
        'session',	
        'term',	
    ];

        public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function class(){
        return $this->belongsTo(StudentClasses::class, 'student_classes_id');
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

}

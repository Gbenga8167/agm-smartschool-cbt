<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
     protected $fillable = [
        'subject_name',
        'status', 		
    ];


    public function studentClasses()
{
    return $this->belongsToMany(
        StudentClasses::class,
        'student_class_subject',
        'subject_id',
        'student_classes_id'
    );
}


 //raltionship for teacher's CBT(A teacher is assigned 
     //to many subject and a subject and class have many teacher)
     public function assignedTeachers(){
        return $this->belongsToMany(Teacher::class, 'assigned_class_subject_teachers',
         'subject_id', //foreign key on pivot table pointing to subjects
          'teacher_id'//foreign key on pivot table pointing to teachers
          );
    }
}


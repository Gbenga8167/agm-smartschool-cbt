<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClasses extends Model
{
    protected $fillable = [
        'class_name',
        'status', 		
    ];



    public function subjects()
{
    return $this->belongsToMany(
        Subject::class,
        'student_class_subject',
        'student_classes_id',
        'subject_id'
    );
}


   // retationship between class and their respective teachers to
    // handle some special task which other subject teacher can not handle 
    
    public function classTeacher(){
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    


    //raltionship for teacher's CBT(A teacher is assigned 
     //to many subject and a subject and class have many teacher)
     public function assignedTeachers(){
        return $this->belongsToMany(Teacher::class, 'assigned_class_subject_teachers',
         'student_classes_id', //foreign key on pivot table pointing to classes
          'teacher_id'//foreign key on pivot table pointing to teachers
          );
    }


}

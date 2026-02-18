<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
     protected $fillable = [
        'user_id',
        'name',
        'photo', 		
    ];

     public function user(){
        return $this->belongsTo(User::class);
    }

    //ASSIGN CLASS RELATIONSHIP
    public function assignment(){
        return $this->hasMany(classsubjectteacher::class);
    }


    //ASSIGN SUBJECT TEACHER RELATIONSHIP
    //TO DETERMINE THE ASSIGNED SUBJECT TO TEACHER
    
    public function assignedsubjectteacher(){
        return $this->hasMany(AssignedClassSubjectTeacher::class, 'teacher_id');
    }


    // retationship between class and their respective teachers to
    // handle some special task which other subject teacher can not handle 
    
    public function class(){
        return $this->hasMany(StudentClasses::class, 'class_teacher_id');
    }




    //raltionship for teacher's CBT(A teacher is assigned 
     //to many subject and a subject and class have many teacher)
     public function assignedSubjects(){
        return $this->belongsToMany(Subject::class, 'assigned_class_subject_teachers',
         'subject_id', //foreign key on pivot table pointing to subject
          'teacher_id'//foreign key on pivot table pointing to teachers
          );
    }



    //raltionship for teacher's CBT(A teacher is assigned 
     //to many subject and a subject and class have many teacher)
     public function assignedClasses(){
        return $this->belongsToMany(Classes::class, 'assigned_class_subject_teachers',
         'student_class_id', //foreign key on pivot table pointing to classes
          'teacher_id'//foreign key on pivot table pointing to teachers
          );
    }



    //SUBJECTS ASSIGNED DISPLAYED IN THE TEACHER DASHBOARD
    public function teacherSubjects()
{
    return $this->belongsToMany(
        Subject::class,
        'assigned_class_subject_teachers',
        'teacher_id', // pivot column that refers to teacher
        'subject_id'  // pivot column that refers to subject
    );
}

//cLASSES ASSIGNED DISPLAYED IN THE TEACHER DASHBOARD
public function teacherClasses()
{
    return $this->belongsToMany(
        StudentClasses::class,
        'assigned_class_subject_teachers',
        'teacher_id', // pivot column that refers to teacher
        'student_class_id'    // pivot column that refers to class
    );
}

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
     protected $fillable = [
        'name',
        'gender',
        'photo',
        'student_classes_id',
    ];


     //declaring relationship btw classes model and student model
    public function class(): BelongsTo{

        return $this->belongsTo(StudentClasses::class, 'student_class_id', 'id',);
    }


        public function user(){
        return $this->belongsTo(User::class);
    }

        //raltionship for student assigned to class in the current term/session in teacher psychomotor
    public function AssignedClassSubjectToStudents(){
        return $this->hasMany(AssignClassSubjectStudent::class);
    }

}

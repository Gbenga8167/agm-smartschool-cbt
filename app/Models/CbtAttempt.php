<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtAttempt extends Model
{
  protected $fillable = [
        'cbt_test_id',
        'student_id',
        'score',
        'started_at',
        'submitted_at',
        'duration_used',
        'question_order',
        'current_question_index',
        'session_token',
        'status',
    ];

    // ✅ Add this line to cast timestamps as Carbon instances
    protected $dates = ['started_at','submitted_at','created_at','updated_at'];

    // belongs to a CBT test
    public function test(){
        return $this->belongsTo(CbtTest::class, 'cbt_test_id');
    }

    // belongs to a student
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    // has many answers
    public function answers(){
        return $this->hasMany(CbtAnswer::class, 'cbt_attempt_id');
    }
}

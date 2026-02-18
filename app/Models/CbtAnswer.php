<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtAnswer extends Model
{
     protected $fillable = [
        'cbt_attempt_id',
        'cbt_question_id',
        'selected_option',
        'is_correct', 
    ];
    

    // belongs to an attempt
    public function attempt(){
        return $this->belongsTo(Cbtttempt::class, 'cbt_attempt_id');
    }

        // belongs to an question
        public function question(){
            return $this->belongsTo(CbtQuestion::class, 'cbt_question_id');
        }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtQuestion extends Model
{
       protected $fillable = [
        'cbt_test_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'mark',
        		
    ];

    //A question belongs to a CBT test
    public function test(){
        return $this->belongsTo(CbtTest::class, 'cbt_test_id');
    }

}

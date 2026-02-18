<?php

namespace App\Http\Controllers\Student;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use App\Models\AssignClassSubjectStudent;

class StudentAccountController extends Controller
{
    // LOGGED OUT STUDENT
    public function StudentLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }//end method


    public function StudentProfile(){

        //GET AUTHENTICATED LOGGED IN USERS(STUDENT)
        $id = Auth::user()->id;
        $StudentData = User::findOrFail($id);
        $studentphoto = Student::where('user_id', $id)->first();
        return view('backend.student_backend.student_profile_view', compact('StudentData', 'studentphoto'));
    
    
    }//end method


     //STUDENT SUBJECTS VIEW
     public function StudentSubjects()
    {
        // Logged-in student
        $student = Student::where('user_id', Auth::id())->first();

        // Current term & session
        //$currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
       // $currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

        // Get subjects for this student
        $subjectIds = AssignClassSubjectStudent::where('student_id', $student->id)
            //->where('term', $currentTerm)
            //->where('session', $currentSession)
            ->pluck('subject_id');

        $subjects = Subject::whereIn('id', $subjectIds)->get();

        //return view('backend.student_backend.student_subjects', compact('subjects', 'currentTerm', 'currentSession'));
              return view('backend.student_backend.student_subjects', compact('subjects'));
    }
}

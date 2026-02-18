<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Term;
use App\Models\CbtTest;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\CbtAttempt;
use App\Models\CbtQuestion;
use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TeacherCbtTestController extends Controller
{
    public function CBTCreate(){

       
        //Fetch only the current term and session (admin controlled)
        $terms = Term::where('is_current', true)->get();
        $sessions = AcademicSession::where('is_current', true)->get();

        $user = Auth::user();
        $teacher  = $user->teacher;
        if(! $teacher){
            abort(403, 'only teachers can create CBT tests');
        }

        $teacherId = $teacher->id;
        //only subjects and classes assigned to this teacher
        $assignedSubject = Subject::whereHas('assignedTeachers', function ($query) use ($teacherId){
            $query->where('teacher_id', $teacherId); 
        })->get();


        $assignedClasses = StudentClasses::whereHas('assignedTeachers', function($query) use ($teacherId){
            $query->where('teacher_id', $teacherId); 
        })->get();
        return view('backend.teacher_backend.cbt_test_question.cbt_test_create', compact('assignedSubject', 'assignedClasses', 'sessions', 'terms'));
    
    
    }//end method



//craete store cbt test by teacher
    public function CBTStore(Request $request){
       
        $request->validate([
            'title' => 'required|string',
            'class_id' => 'required|exists:student_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
            'session' => 'required|string',
            'duration_minutes' => 'required|integer',
            'assessment_type' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'date|nullable',
        ]);

        $user = Auth::user();
        $teacher  = $user->teacher;
        $teacherId = $teacher->id;


        //check if teacher is actually assigned to this class and subject
        $isAssigned = DB::table('assigned_class_subject_teachers')
        ->where('teacher_id', $teacherId)
        ->where('student_classes_id', $request->class_id)
        ->where('subject_id', $request->subject_id)
        ->exists();

        if(!$isAssigned){

             $notification = array(
                'message' => 'You are not assigned to this subject in the selected class.',
                'alert-type' => 'error'
            );
        
            //redirect back to same page
        
           return redirect()->back()->with($notification );
        }


         //check duplicated CBTTest(subject,class,term and session already exist?)
         $exixts = CbtTest::where('student_classes_id', $request->class_id)
         ->where('subject_id', $request->subject_id)
         ->where('term', $request->term)
         ->where( 'session', $request->session,)  
         ->exists();
 
         if($exixts){
 
              $notification = array(
                 'message' => 'A CBT for this class, subject, term, and session already exist.',
                 'alert-type' => 'error'
             );
         
             //redirect back to same page
         
            return redirect()->back()->with($notification );
         }

        CbtTest::create([
            'title' => $request->title,
            'student_classes_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'term' => $request->term,
            'session' => $request->session,
            'duration_minutes' => $request->duration_minutes,
            'assessment_type' => $request->assessment_type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'teacher_id' =>$teacherId,
        ]);

        $notification = array(
            'message' => 'CBT Test created successfully!',
            'alert-type' => 'success'
        );
    
        //redirect back to same page
    
       return redirect()->back()->with($notification );
       
    }//end method for cbt test created by teacher


       //teacher cbt logic to show cbt test created by the logged-in Teacher
    public function Index(){

        $user = Auth::user();
        $teacher  = $user->teacher;
        $teacherId = $teacher->id;

        // Get current term & session
        $currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
        $currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');


        $cbtTests = CbtTest::where('teacher_id', $teacherId)
        ->with(['subject', 'class'])//eager load relationships  
        ->where('term', $currentTerm)
        ->where('session', $currentSession)
        ->orderBy('created_at', 'desc')->get();

        return view('backend.teacher_backend.cbt_test_question.cbt_index', compact('cbtTests'));

    }//end method  
    




    
   //create CBT question for teacher controller 
    public function CreateQuestions($cbtTestId){
        $cbtTest = CbtTest::with(['class', 'subject'])->findOrFail($cbtTestId);

        return view('backend.teacher_backend.cbt_test_question.create_questions', compact('cbtTest'));

    }//end method 


     //store CBT question for teacher controller 
     public function StoreQuestions(Request $request, $cbtTestId){
        $cbtTest = CbtTest::findOrFail($cbtTestId);

        $validate = $request->validate([
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'required|string',
            'questions.*.option_d' => 'required|string',
            'questions.*.correct_option' => 'required|in:A,B,C,D',
            'questions.*.mark' => 'required|numeric|min:1',
        ]);

        foreach($validate['questions'] as $question){
            CbtQuestion::create([
                'cbt_test_id' => $cbtTest->id,
                'question_text' => $question['question_text'],
                'option_a' => $question['option_a'],
                'option_b' => $question['option_b'],
                'option_c' => $question['option_c'],
                'option_d' => $question['option_d'],
                'correct_option' => $question['correct_option'],
                'mark' => $question['mark'],
            ]);
        }

        $notification = array(
            'message' => 'Question added successfully!!',
            'alert-type' => 'success'
        );
    
        //redirect back to same page
    
       return redirect()->back()->with($notification );

    }//end method StoreQuestions



//CSV PREVIEW 

public function previewCsv(Request $request, $cbtTestId){

    $cbtTest = CbtTest::findOrFail($cbtTestId);

    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');
    $content = file_get_contents($file->getRealPath());
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');

    $rows = array_map('str_getcsv', explode("\n", trim($content)));

    // Remove header
    array_shift($rows);

    $validatedRows = [];
    $errors = [];

    foreach ($rows as $index => $row) {

        if (count($row) < 6) {
            $errors[] = "Row " . ($index + 2) . " is incomplete.";
            continue;
        }

        $data = [
            'question_text'  => !empty(trim($row[0])) ? trim($row[0]) : null,
            'option_a'       => !empty(trim($row[1])) ? trim($row[1]) : null,
            'option_b'       => !empty(trim($row[2])) ? trim($row[2]) : null,
            'option_c'       => !empty(trim($row[3] ?? '')) ? trim($row[3]) : null,
            'option_d'       => !empty(trim($row[4] ?? '')) ? trim($row[4]) : null,
            'correct_option' => !empty(trim($row[5])) ? strtolower(trim($row[5])) : null,
            'mark'           => isset($row[6]) && is_numeric($row[6]) ? $row[6] : 1,
            'row_number'     => $index + 2
        ];

        $validator = Validator::make($data, [
            'question_text'  => 'required|string|min:1',
            'option_a'       => 'required|string|min:1',
            'option_b'       => 'required|string|min:1',
            'option_c'       => 'required|string|min:1',
            'option_d'       => 'required|string|min:1',
            'correct_option' => 'required|in:a,b,c,d',
            'mark'           => 'nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $errors[] = "Row {$data['row_number']} has invalid data.";
            continue;
        }

        $validatedRows[] = $data;
    }

    Session::put('csv_preview', $validatedRows);

    return view('backend.teacher_backend.cbt_test_question.csv_preview', compact(
        'cbtTest',
        'validatedRows',
        'errors'
    ));
}

//CSV CONFIRM AND SAVE

public function confirmCsv($cbtTestId)
{
    $cbtTest = CbtTest::findOrFail($cbtTestId);

    $rows = Session::get('csv_preview');

    if (!$rows || count($rows) === 0) {
        return redirect()
            ->route('cbt.questions.create', $cbtTest->id)
            ->with([
                'message' => 'No valid CSV data to save.',
                'alert-type' => 'error'
            ]);
    }

    foreach ($rows as $row) {
        CbtQuestion::create([
            'cbt_test_id'     => $cbtTest->id,
            'question_text'  => $row['question_text'],
            'option_a'       => $row['option_a'],
            'option_b'       => $row['option_b'],
            'option_c'       => $row['option_c'],
            'option_d'       => $row['option_d'],
            'correct_option' => $row['correct_option'],
            'mark'           => $row['mark'],
        ]);
    }

    Session::forget('csv_preview');

    return redirect()
        ->route('cbt.questions.create', $cbtTest->id)
        ->with([
            'message' => count($rows) . ' questions saved successfully!',
            'alert-type' => 'success'
        ]);
}



   
 
 //EDIT CBTCREATE TEST 
    public function EditCBTtCreate($id){
    $terms = Term::where('is_current', true)->get();
    $sessions = AcademicSession::where('is_current', true)->get();

    $user = Auth::user();
    $teacher = $user->teacher;

    if (! $teacher) {
        abort(403, 'Only teachers can edit CBT tests');
    }

    $teacherId = $teacher->id;

    $cbtTest = CbtTest::where('id', $id)
        ->where('teacher_id', $teacherId)
        ->firstOrFail();

    $assignedSubject = Subject::whereHas('assignedTeachers', function ($query) use ($teacherId) {
        $query->where('teacher_id', $teacherId);
    })->get();

    $assignedClasses = StudentClasses::whereHas('assignedTeachers', function ($query) use ($teacherId) {
        $query->where('teacher_id', $teacherId);
    })->get();

    return view(
        'backend.teacher_backend.cbt_test_question.edit_cbt_test_create',
        compact('cbtTest', 'assignedSubject', 'assignedClasses', 'sessions', 'terms')
    );
}
//end edit cbtCRETE test





public function UpdateCBTCreate(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string',
        'class_id' => 'required|exists:student_classes,id',
        'subject_id' => 'required|exists:subjects,id',
        'term' => 'required|string',
        'session' => 'required|string',
        'duration_minutes' => 'required|integer',
        'assessment_type' => 'required|string',
        'start_time' => 'required|date',
        'end_time' => 'nullable|date',
    ]);

    $user = Auth::user();
    $teacher = $user->teacher;
    $teacherId = $teacher->id;

    $cbtTest = CbtTest::where('id', $id)
        ->where('teacher_id', $teacherId)
        ->firstOrFail();

    // ensure teacher is assigned
    $isAssigned = DB::table('assigned_class_subject_teachers')
        ->where('teacher_id', $teacherId)
        ->where('student_classes_id', $request->class_id)
        ->where('subject_id', $request->subject_id)
        ->exists();

    if (! $isAssigned) {
        return redirect()->back()->with([
            'message' => 'You are not assigned to this subject in the selected class.',
            'alert-type' => 'error'
        ]);
    }

    $cbtTest->update([
        'title' => $request->title,
        'student_classes_id' => $request->class_id,
        'subject_id' => $request->subject_id,
        'term' => $request->term,
        'session' => $request->session,
        'duration_minutes' => $request->duration_minutes,
        'assessment_type' => $request->assessment_type,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
    ]);

    return redirect()->route('cbt.test.index')->with([
        'message' => 'CBT Test updated successfully!',
        'alert-type' => 'success'
    ]);
}













// Edit ALL CBTQuestion
public function edit($id)
{
    $cbtTest = CbtTest::with('questions')->findOrFail($id); // Fetch the test and its related questions
    return view('backend.teacher_backend.cbt_test_question.cbt_questions_edit', compact('cbtTest'));
}

//EDIT SPECIFIC QUESTION
public function EditSpecificQuestion($id)
{
    $question = CbtQuestion::findOrFail($id); // Fetch the question by ID
    return view('backend.teacher_backend.cbt_test_question.update_specific_question', compact('question'));
}

// UPDATE SPECIFIC QUESTION
public function update(Request $request, $id)
{
    $question = CbtQuestion::findOrFail($id);

    $request->validate([
        'question_text' => 'required|string',
        'option_a' => 'required|string',
        'option_b' => 'required|string',
        'option_c' => 'required|string',
        'option_d' => 'required|string',
        'correct_option' => 'required|string|in:A,B,C,D',
        'mark' => 'required|numeric',
    ]);

    $question->update([
        'question_text' => $request->question_text,
        'option_a' => $request->option_a,
        'option_b' => $request->option_b,
        'option_c' => $request->option_c,
        'option_d' => $request->option_d,
        'correct_option' => $request->correct_option,
        'mark' => $request->mark,
    ]);

    return redirect()->back()->with('success', 'Question updated successfully!');
  
   
}// end method



//Delete CBTQuestion
public function destroy($id)
{
    $question = CbtQuestion::findOrFail($id);
    $question->delete();

    return redirect()->back()->with('success', 'Question deleted successfully!');
}



public function DestroyAllCBTTestQuestion($id)
{

    $cbtTest = CbtTest::with('questions')->findOrFail($id);

    // Delete all related questions
    foreach ($cbtTest->questions as $question) {
        $question->delete();
    }

    // Delete the CBT test
    $cbtTest->delete();

    return redirect()->back()->with('success', 'CBT Test and all related questions deleted successfully!');
}





  /** TEACHER CBT RESULTS CHECK
     * Show the CBT results filter form
     */
        // Show list of CBT tests created by this teacher
/*public function results()
{
    // current term & session
    $currentTerm = \DB::table('terms')->where('is_current', 1)->value('name');
    $currentSession = \DB::table('academic_sessions')->where('is_current', 1)->value('name');

    // logged-in teacher
    $teacher = \App\Models\Teacher::where('user_id', auth()->id())->first();

    // fetch ONLY results for this teacher's CBT tests
    $results = \DB::table('cbt_attempts')
        ->join('cbt_tests', 'cbt_attempts.cbt_test_id', '=', 'cbt_tests.id')
        ->join('students', 'cbt_attempts.student_id', '=', 'students.id')
        ->join('users', 'students.user_id', '=', 'users.id')
        ->join('student_classes', 'cbt_tests.student_classes_id', '=', 'student_classes.id')
        ->join('subjects', 'cbt_tests.subject_id', '=', 'subjects.id')
        ->select(
            'users.name as student_name',
            'student_classes.class_name as class_name',
            'subjects.subject_name as subject_name',
            'cbt_tests.term',
            'cbt_tests.session',
            'cbt_tests.assessment_type',
            'cbt_attempts.score',
            'cbt_attempts.id as attempt_id'
        )
        ->where('cbt_tests.term', $currentTerm)
        ->where('cbt_tests.session', $currentSession)
        ->where('cbt_tests.teacher_id', $teacher->id) // 🔑 restrict by teacher
        ->get();

    return view('backend.teacher_backend.cbt_test_question.cbt_results_index', compact('results', 'currentTerm', 'currentSession'));
}
*/
        
      public function retake($attemptId)
{
    try {
        \DB::transaction(function () use ($attemptId) {
            $attempt = \DB::table('cbt_attempts')->where('id', $attemptId)->first();

            if (!$attempt) {
                throw new \Exception('Attempt not found.');
            }  

            // Delete all answers linked to this attempt
            \DB::table('cbt_answers')->where('cbt_attempt_id', $attemptId)->delete();

            // Delete the attempt itself
            \DB::table('cbt_attempts')->where('id', $attemptId)->delete();
        });

        return redirect()
    ->route('teacher.cbt.form')
    ->with([
        'message' => 'Previous attempt deleted. Student can now retake the test afresh.',
        'alert-type' => 'success'
    ]);


    } catch (\Exception $e) {
        return back()->with([
            'message' => $e->getMessage(),
            'alert-type' => 'error'
        ]);

        
    }
}









  // Show filter form
    public function CbtTestindex() {
        $teacher = Auth::user()->teacher;
        if (!$teacher) abort(403, 'Only teachers can view results');

        // Fetch classes assigned to teacher
        $assignedClasses = StudentClasses::whereHas('assignedTeachers', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();

        return view('backend.teacher_backend.cbt_test_question.cbt_results_index', compact('assignedClasses'));
    }

    // Fetch subjects based on class (AJAX)
    public function fetchSubjects($classId) {
        $teacher = Auth::user()->teacher;

        $subjects = Subject::whereHas('assignedTeachers', function($q) use ($teacher, $classId) {
            $q->where('teacher_id', $teacher->id)
              ->where('student_classes_id', $classId);
        })->get();

        return response()->json($subjects);
    }

    // Fetch cbt results based on class & subject
// Fetch cbt results based on class & subject
public function fetchResults(Request $request)
{
    // current term & session (admin-controlled)
    $currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
    $currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

    $request->validate([
        'class_id' => 'required|exists:student_classes,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    $teacher = Auth::user()->teacher;

    $results = CbtAttempt::whereHas('test', function ($q) use (
        $request,
        $teacher,
        $currentTerm,
        $currentSession
    ) {
        $q->where('teacher_id', $teacher->id)
          ->where('student_classes_id', $request->class_id)
          ->where('subject_id', $request->subject_id)
          ->where('term', $currentTerm)
          ->where('session', $currentSession);
    })
    ->with([
        'student',
        'test' => function ($q) {
            $q->with('subject', 'class');
        }
    ])
    ->get();

    return view('backend.teacher_backend.cbt_test_question.cbt_results_index', [
        'assignedClasses' => StudentClasses::whereHas('assignedTeachers', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get(),
        'results' => $results,
        'selectedClass' => $request->class_id,
        'selectedSubject' => $request->subject_id,
    ]);
}




}

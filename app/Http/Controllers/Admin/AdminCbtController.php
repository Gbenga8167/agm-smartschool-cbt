<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\CbtAttempt;
use App\Models\CbtQuestion;
use App\Models\CbtTest;
use App\Models\StudentClasses;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminCbtController extends Controller
{
      public function AdminCBTCreate(){
    // Fetch current term & session (admin controlled)
    $terms = Term::where('is_current', true)->get();
    $sessions = AcademicSession::where('is_current', true)->get();

    // Admin can see ALL classes & subjects
    $classes = StudentClasses::all();
    $subjects = Subject::all();

    return view(
        'backend.admin_backend.cbt_test_question.cbt_test_create',
        compact('classes', 'subjects', 'sessions', 'terms')
    );
}
//end method


//getTeachersByClassAndSubject

public function getTeachersByClassAndSubject($classId, $subjectId)
{
    $teachers = Subject::where('id', $subjectId)
        ->whereHas('assignedTeachers', function ($query) use ($classId) {
            $query->where('student_classes_id', $classId);
        })
        ->with(['assignedTeachers' => function ($query) use ($classId) {
            $query->where('student_classes_id', $classId)
                  ->select('teachers.id', 'teachers.name');
        }])
        ->first()
        ?->assignedTeachers ?? collect();

    return response()->json($teachers);
}


//Admin Store Cbt

public function AdminCBTStore(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'class_id' => 'required|exists:student_classes,id',
        'subject_id' => 'required|exists:subjects,id',
        'teacher_id' => 'required|exists:teachers,id', // ✅ REQUIRED NOW
        'term' => 'required|string',
        'session' => 'required|string',
        'duration_minutes' => 'required|integer',
        'assessment_type' => 'required|string',
        'start_time' => 'required|date',
        'end_time' => 'nullable|date',
    ]);

    // Prevent duplicate CBT for SAME teacher, class, subject, term, session
    $exists = CbtTest::where('student_classes_id', $request->class_id)
        ->where('subject_id', $request->subject_id)
        ->where('teacher_id', $request->teacher_id)
        ->where('term', $request->term)
        ->where('session', $request->session)
        ->exists();

    if ($exists) {
        return redirect()->back()->with([
            'message' => 'A CBT already exists for this teacher, class, subject, term, and session.',
            'alert-type' => 'danger'
        ]);
    }

    CbtTest::create([
        'title' => $request->title,
        'student_classes_id' => $request->class_id,
        'subject_id' => $request->subject_id,
        'teacher_id' => $request->teacher_id, // ✅ ASSIGNED
        'term' => $request->term,
        'session' => $request->session,
        'duration_minutes' => $request->duration_minutes,
        'assessment_type' => $request->assessment_type,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
    ]);

    return redirect()->back()->with([
        'message' => 'CBT Test created and assigned to teacher successfully!',
        'alert-type' => 'success'
    ]);
}



public function adminCbtTestIndex(Request $request)
{
    $classes = StudentClasses::orderBy('class_name')->get();

    $cbtTests = collect(); // empty by default
    $selectedClass = null;

    if ($request->filled('class_id')) {
        $currentTerm = Term::where('is_current', 1)->value('name');
        $currentSession = AcademicSession::where('is_current', 1)->value('name');

        $selectedClass = StudentClasses::find($request->class_id);

        $cbtTests = CbtTest::with(['class', 'subject'])
            ->where('student_classes_id', $request->class_id)
            ->where('term', $currentTerm)
            ->where('session', $currentSession)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    return view('backend.admin_backend.cbt_test_question.cbt_index', compact(
        'classes',
        'cbtTests',
        'selectedClass'
    ));
}



    
   //create CBT question for teacher controller 
    public function AdminCreateQuestions($cbtTestId){
        $cbtTest = CbtTest::with(['class', 'subject'])->findOrFail($cbtTestId);

        return view('backend.admin_backend.cbt_test_question.create_questions', compact('cbtTest'));

    }//end method 


     //store CBT question for teacher controller 
     public function AdminStoreQuestions(Request $request, $cbtTestId){
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

public function AdminpreviewCsv(Request $request, $cbtTestId){

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

    return view('backend.admin_backend.cbt_test_question.csv_preview', compact(
        'cbtTest',
        'validatedRows',
        'errors'
    ));
}

//CSV CONFIRM AND SAVE

public function AdminconfirmCsv($cbtTestId)
{
    $cbtTest = CbtTest::findOrFail($cbtTestId);

    $rows = Session::get('csv_preview');

    if (!$rows || count($rows) === 0) {
        return redirect()
            ->route('admin.cbt.questions.create', $cbtTest->id)
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
        ->route('admin.cbt.questions.create', $cbtTest->id)
        ->with([
            'message' => count($rows) . ' questions saved successfully!',
            'alert-type' => 'success'
        ]);
}





// Edit ALL CBTQuestion
public function edit($id)
{
    $cbtTest = CbtTest::with('questions')->findOrFail($id); // Fetch the test and its related questions
    return view('backend.admin_backend.cbt_test_question.cbt_questions_edit', compact('cbtTest'));
}

//EDIT SPECIFIC QUESTION
public function AdminEditSpecificQuestion($id)
{
    $question = CbtQuestion::findOrFail($id); // Fetch the question by ID
    return view('backend.admin_backend.cbt_test_question.update_specific_question', compact('question'));
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




public function EditCbtCreate($id)
{
    $cbtTest = CbtTest::findOrFail($id);

    $classes = StudentClasses::all();
    $subjects = Subject::all();

    $terms = Term::where('is_current', true)->first();
    $session = AcademicSession::where('is_current', true)->first();

    return view('backend.admin_backend.cbt_test_question.edit_cbt_test_create', compact(
        'cbtTest',
        'classes',
        'subjects',
        'terms',
        'session'
    ));
}


    // Handle update
    public function UpdateCbtCreate(Request $request, $id){
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

        $cbtTest = CbtTest::findOrFail($id);

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

        return redirect()->route('admin.cbt.tests.index')->with([
            'message' => 'CBT Test updated successfully!',
            'alert-type' => 'success'
        ]);
    }



    // ================= SHOW FILTER PAGE ACHECK RESULT================
    public function index()
    {
        $classes = StudentClasses::all();
        return view('backend.admin_backend.cbt_test_question.cbt_results_index', compact('classes'));
    }

    // ================= FETCH SUBJECTS (AJAX) =================
    public function fetchSubjects($classId)
    {
        $subjects = Subject::whereHas('assignedTeachers', function($q) use ($classId){
            $q->where('student_classes_id', $classId);
        })->get();

        return response()->json($subjects);
    }

    // ================= FETCH RESULTS =================
    public function fetchResults(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:student_classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
        $currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

        $results = CbtAttempt::whereHas('test', function ($q) use ($request, $currentTerm, $currentSession) {
            $q->where('student_classes_id', $request->class_id)
              ->where('subject_id', $request->subject_id)
              ->where('term', $currentTerm)
              ->where('session', $currentSession);
        })
        ->with([
            'student',
            'test.subject',
            'test.class'
        ])
        ->get();

        $classes = StudentClasses::all();

        return view('backend.admin_backend.cbt_test_question.cbt_results_index', [
            'classes' => $classes,
            'results' => $results,
            'selectedClass' => $request->class_id,
            'selectedSubject' => $request->subject_id,
        ]);
    }

    // ================= RETAKE =================
    public function retake($attemptId)
    {
        try {
            DB::transaction(function () use ($attemptId) {

                $attempt = DB::table('cbt_attempts')
                    ->where('id', $attemptId)
                    ->first();

                if (!$attempt) {
                    throw new \Exception('Attempt not found.');
                }

                DB::table('cbt_answers')
                    ->where('cbt_attempt_id', $attemptId)
                    ->delete();

                DB::table('cbt_attempts')
                    ->where('id', $attemptId)
                    ->delete();
            });

            return redirect()
                ->route('cbt.results.form')
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



    // ADMIN EDIT QUESTIONS LIMIT METHOD
    public function editLimit(){
        
    $session = AcademicSession::first();
    return view('backend.admin_backend.cbt_test_question.question_limit', compact('session'));
}

    public function updateLimit(Request $request){

     $request->validate([
        'test_limit' => 'required|integer|min:1'
    ]);

    $session = AcademicSession::first();

    $session->update([
        'test_limit' => $request->test_limit
    ]);

    return back()->with([
        'message' => 'Question limit updated successfully!',
        'alert-type' => 'success'
    ]);
}

}

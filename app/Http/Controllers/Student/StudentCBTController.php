<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use App\Models\Term;
use App\Models\CbtTest;
use App\Models\Student;
use App\Models\CbtAnswer;
use App\Models\CbtAttempt;
use App\Models\CbtQuestion;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AssignClassSubjectStudent;

class StudentCBTController extends Controller
{
    // Display all CBT tests for the student
    public function Index()
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $currentTerm = Term::where('is_current', true)->first()?->name;
        $currentSession = AcademicSession::where('is_current', true)->first()?->name;

        $assignments = AssignClassSubjectStudent::where('student_id', $student->id)
           // ->where('term', $currentTerm)
            //->where('session', $currentSession)
            ->get();

        $classIds = $assignments->pluck('student_classes_id')->toArray();
        $subjectIds = $assignments->pluck('subject_id')->toArray();

        $cbtTests = CbtTest::whereIn('student_classes_id', $classIds)
            ->whereIn('subject_id', $subjectIds)
            ->where('term', $currentTerm)
            ->where('session', $currentSession)
            ->get();

        return view('backend.student_backend.cbt_question.index', compact('cbtTests'));
    }//end method

        // Start Test page
    // Start Test page
    public function StartTest($id)
{
    $cbtTest = CbtTest::findOrFail($id);

    // Current time in UTC
    $nowUtc = now()->utc();

    // IMPORTANT: DB time is assumed to be saved in Africa/Lagos local time.
    // Convert from Africa/Lagos -> UTC before comparing/sending to JS.
    $startTimeUtc = \Carbon\Carbon::parse($cbtTest->start_time, 'Africa/Lagos')->utc();

    $testStatus = $nowUtc->lt($startTimeUtc) ? 'not_started' : 'started';

    return view('backend.student_backend.cbt_question.student_cbt_test', [
        'cbtTest'    => $cbtTest,
        'testStatus' => $testStatus,
        'startTime'  => $startTimeUtc->getTimestampMs(), // UTC ms
        'serverNow'  => $nowUtc->getTimestampMs(),       // UTC ms (for drift correction)
    ]);
}// end method

    public function BeginTest($id){
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $cbtTest = CbtTest::findOrFail($id);

        // ✅ Block if already completed
        $hasAttempt = CbtAttempt::where('student_id', $student->id)
            ->where('cbt_test_id', $cbtTest->id)
            ->where('status', 'completed')
            ->exists();

        if ($hasAttempt) {
            return redirect()->route('student.index')->with([
                'message' => 'You have already completed this test',
                'alert-type' => 'error',
            ]);
        }


        // ✅ Block if test expired
        if ($cbtTest->end_time && now()->utc()->gte(Carbon::parse($cbtTest->end_time, 'Africa/Lagos')->utc())) {
            return redirect()->route('student.index')->with([
                'message' => 'This test has expired.',
                'alert-type' => 'error',
            ]);
        }

        // ✅ Only reuse active attempt
        $attempt = CbtAttempt::where('cbt_test_id', $cbtTest->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

      // Create new attempt
    if (!$attempt) {

        $sessionToken = bin2hex(random_bytes(32));

        $attempt = CbtAttempt::create([
            'cbt_test_id' => $cbtTest->id,
            'student_id'  => $student->id,
            'started_at'  => now()->utc(),
            'status'      => 'in_progress',
            'session_token' => $sessionToken,
        ]);

        session(['cbt_session_'.$attempt->id => $sessionToken]);
    }


    
    //simple  Validate session (prevent multiple browsers) but allows multiple browser
   /*$sessionKey = 'cbt_session_'.$attempt->id;

if ($attempt->session_token) {

    // If this browser has no token stored → block
    if (!session()->has($sessionKey)) {
        return redirect()->route('student.index')->with([
            'message' => 'This test is already active in another browser.',
            'alert-type' => 'error',
        ]);
    }

    // If token mismatch → block
    if (session($sessionKey) !== $attempt->session_token) {
        return redirect()->route('student.index')->with([
            'message' => 'This test is already active in another browser.',
            'alert-type' => 'error',
        ]);
    }
}
*/

//code banking incase my client want their student to use multiple devices for their exam
//  use this block of code insted of the one above to remove the restriction

    // Validate session (prevent multiple browsers)
    $sessionKey = 'cbt_session_'.$attempt->id;

    if ($attempt->session_token) {
        if (!session()->has($sessionKey)) {
            session([$sessionKey => $attempt->session_token]);
        } elseif (session($sessionKey) !== $attempt->session_token) {
            return redirect()->route('student.index')->with([
                'message' => 'This test is already active in another browser.',
                'alert-type' => 'error',
            ]);
        }
    }


            // --- RANDOMIZE QUESTIONS ONLY FIRST TIME WITH NO LIMIT ---
    /*if (!$attempt->question_order) {
        $questions = $cbtTest->questions()->inRandomOrder()->get();
        $attempt->update([
            'question_order' => $questions->pluck('id')->toJson()
        ]);
    } else {
        // Fetch in saved order
        $questionIds = json_decode($attempt->question_order);
        $questions = CbtQuestion::whereIn('id', $questionIds)
                        ->orderByRaw("FIELD(id, ".implode(',', $questionIds).")")
                        ->get();
    }
                        */


              // --- RANDOMIZE QUESTIONS ONLY FIRST TIME WITH LIMITED QUESTIONS(50)---
          if (!$attempt->question_order) {
              // Fetch all questions in random order
              $allQuestions = $cbtTest->questions()->inRandomOrder()->get();
          
              // Limit to 50 questions (or less if teacher sets <50)
              $session = AcademicSession::first();
              $limit = $session->test_limit;
              $selectedQuestions = $allQuestions->take($limit);
          
              // Save the selected question IDs in attempt
              $attempt->update([
                  'question_order' => $selectedQuestions->pluck('id')->toJson()
              ]);
          
              $questions = $selectedQuestions; // pass to view
          } else {
              // Fetch in saved order (for refresh / continue)
              $questionIds = json_decode($attempt->question_order);
              $questions = CbtQuestion::whereIn('id', $questionIds)
                              ->orderByRaw("FIELD(id, ".implode(',', $questionIds).")")
                              ->get();
          }
          
    

        // ✅ TIMER LOGIC → started_at (UTC) + duration
        $effectiveEnd = Carbon::parse($attempt->started_at)->addMinutes($cbtTest->duration_minutes);

        $endTimeMs   = $effectiveEnd->timestamp * 1000;  // send UTC timestamp
        $serverNowMs = now()->utc()->timestamp * 1000;  // send current UTC timestamp

        return view('backend.student_backend.cbt_question.questions', [
            'cbtTest'   => $cbtTest,
            'attempt'   => $attempt,
            'questions' => $questions,
            'endTime'   => $endTimeMs,
            'serverNow' => $serverNowMs,
            'currentIndex' => $attempt->current_question_index ?? 0,
        ]);
    }



    // Avoid multiple browser usage by student
    public function saveProgress(Request $request, CbtAttempt $attempt){
    $sessionKey = 'cbt_session_'.$attempt->id;

    if ($attempt->session_token !== session($sessionKey)) {
        return response()->json(['error' => 'Invalid session'], 403);
    }

    $attempt->update([
        'current_question_index' => $request->index
    ]);

    return response()->json(['success' => true]);
}











        // Save an answer (AJAX)
    // Save an answer (AJAX)
public function saveAnswer(Request $request, $attemptId, $questionId)
{
    $student = Student::where('user_id', Auth::id())->firstOrFail();

    $attempt = CbtAttempt::where('id', $attemptId)
        ->where('student_id', $student->id)
        ->where('status', 'in_progress')
        ->firstOrFail();

    $cbtTest = $attempt->test; // related test
    $now     = now()->utc();

    // ✅ Compute effective end time: started_at + duration
    $effectiveEnd = Carbon::parse($attempt->started_at)->addMinutes($cbtTest->duration_minutes);

    // ✅ If test has a global end_time, respect it too
        $absoluteEnd = $cbtTest->end_time
        ? Carbon::parse($cbtTest->end_time, 'Africa/Lagos')->utc()
        : null;

    // ✅ Final deadline = min(effectiveEnd, absoluteEnd if exists)
    $finalDeadline = $absoluteEnd 
        ? ($effectiveEnd->lt($absoluteEnd) ? $effectiveEnd : $absoluteEnd)
        : $effectiveEnd;

    // 🔒 Block saving after deadline
    if ($now->gt($finalDeadline)) {
        return response()->json([
            'success' => false,
            'message' => 'Time expired. You cannot save more answers.',
        ], 403);
    }

    $question = CbtQuestion::findOrFail($questionId);

    $selected  = strtolower($request->input('selected_option')); // 'a' | 'b' | 'c' | 'd'
    $isCorrect = $selected === strtolower($question->correct_option);

    CbtAnswer::updateOrCreate(
        [
            'cbt_attempt_id'  => $attempt->id,
            'cbt_question_id' => $question->id
        ],
        [
            'selected_option' => $selected,
            'is_correct'      => $isCorrect
        ]
    );

    return response()->json(['success' => true]);
}


    // Submit test (manual or auto)
public function submitTest(Request $request, $attemptId)
{
    $student = Student::where('user_id', Auth::id())->firstOrFail();

    $attempt = CbtAttempt::where('id', $attemptId)
        ->where('student_id', $student->id)
        ->firstOrFail();

    $cbtTest = $attempt->test; // fetch related test
    $now     = now()->utc();

    // ✅ If already completed, return politely
    if ($attempt->status === 'completed') {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'score'   => (int)($attempt->score ?? 0),
                'message' => 'Already submitted.',
            ]);
        }
        return redirect()->route('student.index')
            ->with('success', 'Test already submitted. Your score: ' . (int)($attempt->score ?? 0));
    }

    // ✅ Compute effective end time: started_at + duration
    $effectiveEnd = Carbon::parse($attempt->started_at)->addMinutes($cbtTest->duration_minutes);

    // ✅ If test has a global end_time, respect it too
        $absoluteEnd = $cbtTest->end_time
        ? Carbon::parse($cbtTest->end_time, 'Africa/Lagos')->utc()
        : null;


    // ✅ Final deadline = min(effectiveEnd, absoluteEnd if exists)
    $finalDeadline = $absoluteEnd 
        ? $effectiveEnd->lt($absoluteEnd) ? $effectiveEnd : $absoluteEnd 
        : $effectiveEnd;

    // ✅ If student tries to submit after deadline → auto-force complete with saved answers only
    if ($now->gt($finalDeadline)) {
        $score = $attempt->answers()->where('is_correct', true)->count();

        $attempt->update([
            'score'         => $score,
            'submitted_at'  => $now,
            'duration_used' => $now->diffInMinutes($attempt->started_at),
            'status'        => 'completed',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'score'   => $score,
                'message' => 'Submission closed. Auto-submitted by server.',
            ]);
        }

        return redirect()->route('student.index')
            ->with('error', 'Time is up! Your test was auto-submitted. Score: ' . $score);
    }

    // ✅ Normal on-time submission
    $score = $attempt->answers()->where('is_correct', true)->count();

    $attempt->update([
        'score'         => $score,
        'submitted_at'  => $now,
        'duration_used' => $now->diffInMinutes($attempt->started_at),
        'status'        => 'completed',
    ]);

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'score'   => $score,
            'message' => 'Submitted successfully',
        ]);
    }

    return redirect()->route('student.index')
        ->with('success', 'Test submitted successfully! Your score: ' . $score);
}





}

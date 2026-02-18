@extends('backend.admin_backend.admin_dashboard')
@section('admin')


@php
use App\Models\Student;
use App\Models\StudentClasses;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\CbtTest;
use App\Models\CbtAttempt;
use Illuminate\Support\Facades\DB;



// Student Growth per month (current year)
$monthlyStudents = [];
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

foreach($months as $index => $month){
    $monthlyStudents[] = Student::whereYear('created_at', date('Y'))
                                ->whereMonth('created_at', $index+1)
                                ->count();
}

// Teachers per class
$classes = StudentClasses::with('classTeacher')->get();
$teacherNames = $classes->map(fn($c) => $c->classTeacher ? $c->classTeacher->name : 'Unassigned'); // Assuming one teacher per class in your setup
$classNames = $classes->pluck('class_name'); // Adjust according to your column name





// Current term & session
$currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
$currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

// ================= CBT ANALYTICS (Current Term & Session) =================

// Total Tests (only current term/session)
$totalTests = CbtTest::where('term', $currentTerm)
                     ->where('session', $currentSession)
                     ->count();

// Total Attempts (only attempts for tests in current term/session)
$totalAttempts = CbtAttempt::whereHas('test', function($q) use($currentTerm, $currentSession){
                        $q->where('term', $currentTerm)
                          ->where('session', $currentSession);
                    })->count();

// Completed Attempts
$completedAttempts = CbtAttempt::where('status','completed')
                    ->whereHas('test', function($q) use($currentTerm, $currentSession){
                        $q->where('term', $currentTerm)
                          ->where('session', $currentSession);
                    })->count();

// In-progress Attempts
$inProgressAttempts = CbtAttempt::where('status','in_progress')
                    ->whereHas('test', function($q) use($currentTerm, $currentSession){
                        $q->where('term', $currentTerm)
                          ->where('session', $currentSession);
                    })->count();

// Pass vs Fail (adjust pass mark here)
$passCount = CbtAttempt::where('status','completed')
                ->whereHas('test', function($q) use($currentTerm, $currentSession){
                    $q->where('term', $currentTerm)
                      ->where('session', $currentSession);
                })
                ->where('score', '>=', 40)
                ->count();

$failCount = CbtAttempt::where('status','completed')
                ->whereHas('test', function($q) use($currentTerm, $currentSession){
                    $q->where('term', $currentTerm)
                      ->where('session', $currentSession);
                })
                ->where('score', '<', 40)
                ->count();



// ================= Students Participation Per Class =================

$attemptsPerClass = CbtAttempt::whereHas('test', function($q) use($currentTerm, $currentSession){
                        $q->where('term', $currentTerm)
                          ->where('session', $currentSession);
                    })
                    ->join('students', 'cbt_attempts.student_id', '=', 'students.id')
                    ->select(
                        'students.student_classes_id',
                        DB::raw('count(distinct cbt_attempts.student_id) as total')
                    )
                    ->groupBy('students.student_classes_id')
                    ->get();

$classAttemptNames = [];
$classAttemptCounts = [];

foreach ($classes as $class) {

    $classAttemptNames[] = $class->class_name;

    $record = $attemptsPerClass
                ->firstWhere('student_classes_id', $class->id);

    $classAttemptCounts[] = $record ? $record->total : 0;
}


// ================= Average Score per Class (Current Term & Session) =================
// ================= Optimized Average Score per Class =================

$averageScores = [];
$classNames = [];

// Get all students grouped by class
$students = Student::select('id','student_classes_id')->get()
            ->groupBy('student_classes_id');

// Get all completed attempts for current term/session
$attempts = CbtAttempt::where('status','completed')
            ->whereHas('test', function($q) use ($currentTerm, $currentSession) {
                $q->where('term', $currentTerm)
                  ->where('session', $currentSession);
            })
            ->select('student_id','score')
            ->get()
            ->groupBy('student_id');

foreach ($classes as $class) {

    $classNames[] = $class->class_name;

    $classStudents = $students[$class->id] ?? collect();

    $studentCount = $classStudents->count();

    if ($studentCount == 0) {
        $averageScores[] = 0;
        continue;
    }

    $classTotal = 0;

    foreach ($classStudents as $student) {

        $studentAttempts = $attempts[$student->id] ?? collect();

        $studentAvg = $studentAttempts->count() > 0
                        ? $studentAttempts->avg('score')
                        : 0;

        $classTotal += $studentAvg;
    }

    $averageScores[] = round($classTotal / $studentCount, 1);
}

// Sort descending
$combined = array_combine($classNames, $averageScores);
arsort($combined);
$classNames = array_keys($combined);
$averageScores = array_values($combined);
@endphp




@php

$totalstudent = count(App\Models\Student::all());
$totalsubject = count(App\Models\Subject::all());
$totalclass = count(App\Models\StudentClasses::all());
$totalteachers = count(App\Models\Teacher::all());

@endphp

  <style>

/*taller cards*/

.dashboard-card {
    min-height: 160px; /* Increased height */
}

.dashboard-card {
    min-width: 160px; /* Increased height */
}

.dashboard-card .card-body {
    padding: 1.8rem; /* More spacing inside */
}

.dashboard-card h3 {
    font-size: 1.9rem; /* Bigger number */
}


    .dashboard-card {
        transition: all 0.3s ease-in-out;
    }
    .dashboard-card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 12px 25px rgba(0,0,0,0.2) !important;
    }
    .dashboard-card .avatar-sm i {
        transition: transform 0.3s ease-in-out, color 0.3s ease-in-out;
    }
    .dashboard-card:hover .avatar-sm i {
        transform: scale(1.2);
        color: #fff;
    }

        /*Animation Styles*/
    .animate-card {
        opacity: 0;
        transform: translateY(30px);
        animation: popUp 0.8s ease forwards;
        animation-delay: var(--delay, 0s);
    }
    @keyframes popUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container-fluid">
                        
                        <!-- start page title -->
                        <div class="row animate-card" style="--delay:0s;">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Dashboard</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

<div class="row">
    <!-- Total Students -->
    <div class="col-xl-3 col-md-6  animate-card" style="--delay:0.2s;">
        <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold">Total Students</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalstudent }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="ri-user-3-line fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Subjects -->
    <div class="col-xl-3 col-md-6  animate-card" style="--delay:0.4s;">
        <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #00b09b, #96c93d);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold">Total Subjects</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalsubject }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-book-open fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Classes -->
    <div class="col-xl-3 col-md-6  animate-card" style="--delay:0.6s;">
        <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #ff416c, #ff4b2b);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold">Total Classes</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalclass }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="ri-building-line fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Teachers -->
    <div class="col-xl-3 col-md-6  animate-card" style="--delay:0.8s;">
        <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #36d1dc, #5b86e5);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold">Total Teachers</p>
                        <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalteachers }}</h3>
                    </div>
                    <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-book-reader fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>




     <div class="col-xl-3 col-md-6 animate-card" style="--delay:1.2s;">
    <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white"
         style="background: linear-gradient(135deg, #11998e, #38ef7d);">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 fw-semibold">Total CBT Attempts</p>
                    <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalAttempts }}</h3>
                </div>
                <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                    <i class="ri-bar-chart-line fs-3"></i>
                </div>
            </div>
        </div>
    </div>



</div>



    <div class="col-xl-3 col-md-6 animate-card" style="--delay:1s;">
    <div class="card dashboard-card shadow-lg border-0 rounded-4 text-white"
         style="background: linear-gradient(135deg, #ff9966, #ff5e62);">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 fw-semibold">Total CBT Tests</p>
                    <h3 class="fw-bold mb-0" style="color: #fff;">{{ $totalTests }}</h3>
                </div>
                <div class="avatar-sm bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center">
                    <i class="ri-file-list-3-line fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

   
</div>


                        
                   




 <div class="row mt-4">
    <!-- Student Growth Chart -->
    <div class="col-xl-6 col-md-12 animate-card" style="--delay:1s;">
        <div class="card shadow-lg border-0 rounded-4" class="dashboard-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0" style="color: #fff;">Student Growth (Monthly)</h5>
            </div>
            <div class="card-body">
                <canvas id="studentGrowthChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Teachers per Class Chart 
    <div class="col-xl-6 col-md-12 animate-card" style="--delay:1.2s;">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0" style="color: #fff;">Teachers per Class</h5>
            </div>
            <div class="card-body">
                <canvas id="teachersPerClassChart" height="200"></canvas>
            </div>
        </div>
    </div>
-->


<!-- Pass vs Fail -->
    <div class="col-xl-6 col-md-12 animate-card" style="--delay:1.4s;">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0" style="color:#fff;">Students Performances Across All Classes</h5>
            </div>
            <div class="card-body">
                <canvas id="passFailChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 Most Attempted Tests -->
    <div class="col-xl-6 col-md-12 animate-card" style="--delay:1.6s;">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-info text-white">
            <h5 class="mb-0" style="color:#fff;">Student Participation Per Class</h5>
            </div>
            <div class="card-body">
                <canvas id="topTestsChart" height="200"></canvas>
            </div>
        </div>
    </div>


        <!-- Average Scores per Class -->
<div class="col-xl-6 col-md-12 animate-card" style="--delay:1.8s;">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0" style="color: #fff;">Average Scores per Class</h5>
        </div>
        <div class="card-body">
            <canvas id="classAvgChart" height="200"></canvas>
        </div>
    </div>
</div>


    
</div>



         



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Student Growth Chart
    const studentCtx = document.getElementById('studentGrowthChart').getContext('2d');
    const studentGrowthChart = new Chart(studentCtx, {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Students',
                data: @json($monthlyStudents),
                backgroundColor: 'rgba(102, 126, 234, 0.2)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Teachers per Class Chart
  const classNames = @json($classNames);
  const teacherNames = @json($teacherNames);

new Chart(document.getElementById('teachersPerClassChart'), {
    type: 'bar',
    data: {
        labels: classNames,
        datasets: [{
            label: 'Class Teachers',
            data: teacherNames.map(name => 1), // each teacher counts as 1
            backgroundColor: 'rgba(54, 209, 220, 0.7)',
            borderColor: 'rgba(54, 209, 220, 1)',
        }]
    },
    options: {
        indexAxis: 'x',
        plugins: {
            tooltip: {
                callbacks: {
                    label: (context) => teacherNames[context.dataIndex]
                }
            }
        }
    }
});



new Chart(document.getElementById('passFailChart'), {
    type: 'bar',
    data: {
        labels: ['Scores Above 40', 'Scores Below 40'],
        datasets: [{
            label: 'Students Result', // ✅ ADD THIS
            data: [{{ $passCount }}, {{ $failCount }}],
            backgroundColor: ['#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});





new Chart(document.getElementById('topTestsChart'), {
    type: 'bar',
    data: {
        labels: @json($classAttemptNames),
        datasets: [{
            label: 'Attempts',
            data: @json($classAttemptCounts),
            backgroundColor: 'rgba(255, 159, 64, 0.7)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});


// Average Scores per Class Chart
// Average Score per Class
new Chart(document.getElementById('classAvgChart'), {
    type: 'bar',
    data: {
        labels: @json($classNames),
        datasets: [{
            label: 'Average Score',
            data: @json($averageScores),
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true, position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.raw + '%';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) { return value + "%"; }
                }
            }
        }
    }
});



</script>



@endsection
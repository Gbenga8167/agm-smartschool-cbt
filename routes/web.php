<?php

use App\Http\Controllers\Admin\AdminCbtController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClassesController;
use App\Http\Controllers\Admin\SchoolSettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\StudentAccountController;
use App\Http\Controllers\Student\StudentCBTController;
use App\Http\Controllers\Teacher\TeacherAccountController;
use App\Http\Controllers\Teacher\TeacherCbtTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/



// Show login page if guest
Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

// If logged in, redirect to correct dashboard
Route::get('/', function () {
    $user = auth()->user();

    if ($user->role == 1) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role == 2) {
        return redirect()->route('teacher.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->middleware('auth');




    Route::get('/student/dashboard', function () {
    return view('backend.student_backend.student_index');
})->middleware(['auth', 'verified', 'students'])->name('student.dashboard');



    //Start Student All Route  
    Route::middleware(['auth', 'students'])->group(function(){
    //Start Student All Route  
    Route::controller(StudentAccountController::class)->group(function(){
    Route::get('student/logout','StudentLogout')->name('student.logout');
    Route::get('student/profile','StudentProfile')->name('student.profile');
   // Route::get('student/result/form','StudentResultForm')->name('student.result.form');
    //Route::post('student/result/view','StudentResultView')->name('student.result.view');
    Route::get('student/subjects','StudentSubjects')->name('student.subjects');
});

 
    //Student All Route FOR CBTTEST
    Route::controller(StudentCBTController::class)->group(function(){
    Route::get('/student/cbt-tests','Index')->name('student.index');
    Route::get('student/cbt/test/{id}','StartTest')->name('student.cbt.test');
    Route::get('student/cbt/begin/{id}','BeginTest')->name('student.begin.test');
    Route::post('student/cbt/save-answer/{attemptId}/{questionId}', 'saveAnswer')->name('student.cbt.save.answer');
    Route::post('student/cbt/submit/{attemptId}', 'submitTest')->name('student.cbt.submit');
    Route::post('/student/cbt/save-progress/{attempt}', 'saveProgress');



    }); //end Student Route
    
}); // end student route middleware




// START TEACHER ROUTE

    Route::get('/teacher/dashboard', function () {
    return view('backend.teacher_backend.teacher_index');
})->middleware(['auth', 'verified', 'teachers'])->name('teacher.dashboard');




    Route::middleware(['auth', 'teachers'])->group(function(){
    //Teacher All Route
    Route::controller(TeacherAccountController::class)->group(function(){
    Route::get('teacher/logout','TeacherLogout')->name('teacher.logout');
    Route::get('teacher/profile','TeacherProfile')->name('teacher.profile');
    Route::post('teacher/profile/update','TeacherProfileUpdate')->name('teacher.profile.update');
    Route::get('teacher/password/change','TeacherPasswordChange')->name('teacher.password.change');
    Route::post('teacher/password/update','TeacherPasswordUpdate')->name('teacher.password.update');

    //TEACHER ASSIGNED SUBJECT ROUTE

    Route::get('teacher/assigned/subjects','TeacherAssignedSubject')->name('teacher.assigned.subject');

});



    // TEACHER'S CBT CONTROLLER
    //cbt teacher test create
    Route::controller(TeacherCbtTestController::class)->group(function(){
    Route::get('teacher/cbt-tests/create','CBTCreate')->name('cbt.test.create');
    Route::post('teacher/cbt-tests/store','CBTStore')->name('cbt.test.store');
    Route::get('teacher/cbt-tests/index','Index')->name('cbt.test.index');
    //create the question and this cbt.questions.create is used to display question assigned to the teacher 
    //in the cbt_index.blade
    Route::get('teacher/cbt-tests/question/create/{cbtTest}','CreateQuestions')->name('cbt.questions.create');

     //Store the CBT question
     Route::post('teacher/cbt-tests/question/create/{cbtTest}','StoreQuestions')->name('cbt.questions.store');
      

      //Store CSV FILE UPLOAD 
    //Route::post('/cbt-questions/{cbtTest}/upload-csv', 'uploadCsv')->name('cbt.questions.upload.csv');
    // STEP 1: Upload + Preview CSV
     Route::post('/cbt-questions/{cbtTest}/upload-csv', 'previewCsv')->name('cbt.questions.upload.csv');

    // STEP 2: Confirm & Save CSV
     Route::post('/cbt-questions/{cbtTest}/confirm-csv', 'confirmCsv')->name('cbt.questions.csv.confirm');



     // CBT Test edit & update (CBTTEST CREATE)
     Route::get('/teacher/cbt-test/{id}/edit', 'EditCBTtCreate')->name('edit.cbt.test.create');
     Route::post('/teacher/cbt-test/{id}', 'UpdateCBTCreate')->name('update.cbt.test.create');


     //Teacher Cbt questions EDIT, UPDATE AND DELETE CBTQUESTION
     Route::get('/cbt/questions/{id}/edit','edit')->name('cbt.questions.edit');
     Route::delete('/cbt/delete-all-cbt-test-questions/{id}/delete','DestroyAllCBTTestQuestion')->name('cbt.questions.delete.all');
     
     //Teacher Cbt EDIT, UPDATE AND DELETE SPECIFIC CBTQUESTION
     Route::get('/cbt/specific/questions/{id}/edit','EditSpecificQuestion')->name('update.specific.questions');
     Route::post('/cbt/questions/{id}/update','update')->name('cbt.questions.update');
     Route::delete('/cbt/questions/{id}/delete','destroy')->name('cbt.questions.delete');



     //TEACHER CBT RESULT CHECK
     // ===============================
     // Teacher CBT Results Routes
     // =============================
     // Show the CBT Results filter form

     // Allow a student to retake test

    Route::post('/teacher/cbt-results/{attempt}/retake', 'retake')->name('teacher.cbt.retake');







         //TEACHER CBT RESULT  filter CHECK
     // ===============================
     Route::get('/teacher/cbt-results', 'CbtTestindex')->name('teacher.cbt.form');

    // Fetch subjects via AJAX for selected class
    Route::get('/teacher/cbt-results/subjects/{classId}', 'fetchSubjects')->name('teacher.cbt.results.fetchSubjects');

    // Fetch results based on selected class & subject
    Route::post('/teacher/cbt-results/fetch', 'fetchResults')->name('teacher.cbt.results.fetch');

    // Allow a student to retake test
    //Route::post('/cbt-results/{attempt}/retake', 'retake')->name('teacher.cbt.retake');
    
    });
    

});//end teacher route













// Start Admin All Route
    //Admin Dashbord Login Route
    Route::get('/admin/dashboard', function () {
    return view('backend.admin_backend.admin_index');
})->middleware(['auth', 'verified', 'admin'])->name('admin.dashboard');


    //Admin All Route
     Route::middleware(['auth', 'admin'])->group(function(){

    Route::controller(AdminController::class)->group(function(){
    Route::get('admin/logout','AdminLogout')->name('admin.logout');
    Route::get('admin/profile','AdminProfile')->name('admin.profile');
    Route::post('admin/profile/update','AdminProfileUpdate')->name('admin.profile.update');
    Route::get('admin/password/change','AdminPasswordChange')->name('admin.password.change');
    Route::post('admin/password/update','AdminPasswordUpdate')->name('admin.password.update');

});


    //ALL CREATE/ADD CLASSES ROUTE URL
    Route::controller(ClassesController::class)->group(function(){
    Route::get('create/classes','CreateClasses')->name('create.class');
    Route::post('store/classes','StoreClasses')->name('store.classes');
    Route::get('manage/classes','ManageClasses')->name('manage.classes');
    Route::get('edit/class/{id}','EditClass')->name('edit.class');
    Route::post('update/class','UpdateClass')->name('update.class');
    Route::get('delete/class/{id}','DeleteClass')->name('delete.class');

});




    //ALL CREATE/ADD SUBJECT ROUTE
    Route::controller(SubjectController::class)->group(function(){
    Route::get('create/subject','CreateSubject')->name('create.subject');
    Route::post('store/subject','StotreSubject')->name('store.subject');
    Route::get('manage/subject','ManageSubject')->name('manage.subject');
    Route::get('edit/subject/{id}','EditSubject')->name('edit.subject');
    Route::post('update/subject','UpdateSubject')->name('update.subject');
    Route::get('delete/subject/{id}','DeleteSubject')->name('delete.subject');



    
    // Subject Combination All Route
    Route::get('add/subject/combination','AddSubjectCombination')->name('add.subject.combination');
    Route::post('store/subject/combination','StoreSubjectCombination')->name('store.subject.combination');
    Route::get('manage/subject/combination','ManageSubjectCombination')->name('manage.subject.combination');
    Route::get('deactivate/subject/combination/{id}','DeactivateSubjectCombination')->name('deactivate.subject.combination');
    Route::get('delete/subject/combination/{id}','DeleteSubjectCombination')->name('delete.subject.combination');
  


});



 //STUDENT ALL ROUTE
    Route::controller(StudentController::class)->group(function(){
    Route::get('add/student','AddStudent')->name('add.student');
    Route::post('store/student','StoreStudent')->name('store.student');
    Route::get('manage/student','ManageStudent')->name('manage.student');
    Route::get('edit/student/{id}','EditStudent')->name('edit.student');
    Route::post('update/student','UpdateStudent')->name('update.student');
    Route::delete('delete/student/{id}','DeleteStudent')->name('delete.student');

        // delete all student record route
    Route::delete('/delete-all/student','deleteAllStudent')->name('delete.all.student');



        // ASSIGN STUDENT CLASS SUBJECT
    Route::get('assign/student/class/subject', 'AssignStudentClassSubject')->name('assign.student.class.subject');
    Route::post('store/student/class/subject', 'StoreStudentClassSubject')->name('store.student.class.subject');
    Route::get('fetch/subjects', 'FetchSubjects')->name('fetch.subjects');
    Route::get('fetch/students', 'FetchStudents')->name('fetch.students');
    Route::get('manage/assign/student/class/subject', 'ManageAssignStudentClassSubject')->name('manage.assign.student.class.subject');
    Route::get('edit/assign/student/class/subject/{id}', 'EditAssignStudentClassSubject')->name('edit.assign.student.class.subject');
    Route::post('update/assign/student/class/subject', 'UpdateAssignStudentClassSubject')->name('update.assign.student.class.subject');
    Route::delete('delete/assign/student/class/subject/{id}', 'DeleteAssignStudentClassSubject')->name('delete.assign.student.class.subject');

    // delete all 
    Route::delete('/manage/assign/student/class/delete-all','deleteAll')->name('delete.all.assign.student.class.subject');


     //Ajax All Request For Assign Subject To Teacher
     Route::get('fetch/student','FetchStudent')->name('fetch.student');
    
});



 //TEACHERS ALL ROUTE
    Route::controller(TeacherController::class)->group(function(){
    Route::get('add/teacher','AddTeacher')->name('add.teacher');
    Route::post('store/teacher','StoreTeacher')->name('store.teacher');
    Route::get('manage/teacher','ManageTeacher')->name('manage.teacher');
    Route::get('edit/teacher/{id}','EditTeacher')->name('edit.teacher');
    Route::post('update/teacher','UpdateTeacher')->name('update.teacher');
    Route::delete('delete/teacher/{id}','DeleteTeacher')->name('delete.teacher');

    // delete all teachers record route
    Route::delete('/delete-all/teacher','deleteAllTeacher')->name('delete.all.teacher');


    //Add Assign Subject To Teacher
    Route::get('assign/teacher/subject', 'AssignSubjectTeacher')->name('assign.teacher.subject');
    Route::post('store/teacher/subject', 'StoreAssignSubjectTeacher')->name('store.teacher.subject');
    Route::get('manage/assign/subject/teacher', 'ViewAssignSubjectTeacher')->name('manage.assign.subject.teacher');
    Route::get('edit/assign/subject/teacher/{id}', 'EditAssignSubjectTeacher')->name('edit.assign.subject.teacher');
    Route::post('update/assign/subject/teacher', 'UpdateAssignSubjectTeacher')->name('update.assign.subject.teacher');
    Route::delete('delete/assign/subject/teacher/{id}', 'DeleteAssignSubjectTeacher')->name('delete.assign.subject.teacher');

    // delete all teachers record route
    Route::delete('/delete-all/assigned/teacher','deleteAllAssignedTeacher')->name('delete.all..assigned.teacher');

    
    //Ajax All Request For Assign Subject To Teacher
    Route::get('fetch/student','FetchStudent')->name('fetch.student');

});





    Route::controller(SchoolSettingController::class)->group(function(){
    Route::get('/school-settings', 'index')->name('school.settings');
    Route::post('/school-settings/store', 'store')->name('school.settings.store');


    //Academic Session and Term Settings
    Route::get('/academic-settings', 'SessionIndex')->name('session.create');
    Route::post('/academic-settings/session',  'storeSession')->name('academic.session.store');
    Route::post('/academic-settings/session/{id}/toggle', 'toggleSession')->name('academic.session.toggle');
    Route::post('/academic-settings/term/{id}/toggle', 'toggleTerm')->name('academic.term.toggle');
   // Academic Session CRUD
    Route::get('/academic-sessions/{id}/edit', 'edit')->name('academic.session.edit');
    Route::put('/academic-sessions/{id}', 'update')->name('academic.session.update');
    Route::delete('/academic-sessions/{id}', 'destroy')->name('academic.session.destroy');

    });


    //ADMIN CBT ROUTE
    
    Route::controller(AdminCbtController::class)->group(function(){

     Route::get('admin/cbt-tests/create', 'AdminCBTCreate')->name('admin.cbt.test.create');
     Route::post('admin/cbt-tests/store', 'AdminCBTStore')->name('admin.cbt.test.store');
     
    // ✅ NEW: fetch teachers by class & subject
    Route::get('admin/get-teachers/{class}/{subject}', 'getTeachersByClassAndSubject')
    ->name('admin.get.teachers.by.class.subject');

    Route::get('admin/cbt-tests', 'adminCbtTestIndex')
    ->name('admin.cbt.tests.index');

     Route::get('admin/cbt-tests/question/create/{cbtTest}','AdminCreateQuestions')->name('admin.cbt.questions.create');

     //Store the CBT question
     Route::post('admin/cbt-tests/question/create/{cbtTest}','AdminStoreQuestions')->name('admin.cbt.questions.store');
      


    //Store CSV FILE UPLOAD 
    //Route::post('/cbt-questions/{cbtTest}/upload-csv', 'uploadCsv')->name('cbt.questions.upload.csv');
    // STEP 1: Upload + Preview CSV
     Route::post('/admin-cbt-questions/{cbtTest}/upload-csv', 'AdminpreviewCsv')->name('admin.cbt.questions.upload.csv');

    // STEP 2: Confirm & Save CSV
     Route::post('/admin-cbt-questions/{cbtTest}/confirm-csv', 'AdminconfirmCsv')->name('admin.cbt.questions.csv.confirm');



      //Admin Cbt questions EDIT, UPDATE AND DELETE CBTQUESTION
     Route::get('/admin/cbt/questions/{id}/edit','edit')->name('admin.cbt.questions.edit');
     Route::delete('/admin/cbt/delete-all-cbt-test-questions/{id}/delete','DestroyAllCBTTestQuestion')->name('admin.cbt.questions.delete.all');
     

     //Admin Cbt EDIT, UPDATE AND DELETE SPECIFIC CBTQUESTION
     Route::get('/admin/cbt/specific/questions/{id}/edit','AdminEditSpecificQuestion')->name('admin.update.specific.questions');
     Route::post('/admin/cbt/questions/{id}/update','update')->name('admin.cbt.questions.update');
     Route::delete('/admin/cbt/questions/{id}/delete','destroy')->name('admin.cbt.questions.delete');


     //Edit ADMIN CBT TEST CREATE 
    Route::get('/cbt-test/{id}/edit', 'EditCbtCreate')->name('admin.cbt.test.edit');
    Route::post('/cbt-test/{id}', 'UpdateCbtCreate')->name('admin.cbt.test.update');



    //ADMIN CHECK CBT RESULT

    Route::get('/admin/cbt-results', 'index')->name('cbt.results.form');

    Route::get('/admin/cbt-results/subjects/{classId}', 'fetchSubjects')->name('cbt.results.fetchSubjects');

    Route::post('/admin/cbt-results/fetch', 'fetchResults')->name('cbt.results.fetch');

    Route::post('/admin/cbt-results/{attempt}/retake', 'retake')->name('cbt.retake');


    // ADMIN SET QUESTIONS LIMIT
    Route::get('/admin/question-limit', 'editLimit')->name('admin.question.limit');
    Route::post('/admin/question-limit', 'updateLimit')->name('admin.question.limit.update');

    });
  



    

}); //end admin route







    

    Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

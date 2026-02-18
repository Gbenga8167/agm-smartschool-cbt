<?php

namespace App\Http\Controllers\Admin;

use App\Models\Term;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\AssignClassSubjectStudent;

class StudentController extends Controller
{
     public function AddStudent(){
        $classes = StudentClasses::all();

        return view('backend.admin_backend.admin_create_student.add_student_view', compact('classes'));
    }//end method


    public function StoreStudent(Request $request){

       $request->validate([

    // ================= USER TABLE =================
    'full_name' => [
        'required',
        'regex:/^[a-zA-Z ]+$/'
    ],

    'username' => [
        'required',
        'string',
        'unique:users,user_name'
    ],

    'password' => [
        'required','confirmed',
        'min:6'
    ],

    'role' => [
        'required',
        'in:3' // student only
    ],


     'class_id' => [
        'required',
        'exists:student_classes,id'
    ],
    // ================= STUDENT TABLE =================

  
    // ================= PHOTO =================
    'photo' => [
        'nullable',
        'image',
        'mimes:jpg,jpeg,png',
        'max:2048'
    ],

], [

    // ================= CUSTOM MESSAGES =================
    'full_name.regex' => 'Student name must contain only letters and spaces.',
    'username.regex' => 'Username can contain only letters, numbers, and underscore.',
    'email.email' => 'Please enter a valid email address.',
    'email.dns' => 'Email domain does not exist.',
    'password.confirmed' => 'Password confirmation does not match.',
]);



      // here student_id is used inplace of student username used to 
      // disallowed duplication of student
      
    $AlreadyExist = User::where('user_name', $request->username)->first();
    if($AlreadyExist){

             $notification = array(
            'message' => ' Username Already exist',
            'alert-type' => 'info'
        );

        //redirect back to same page

  return redirect()->back()->with($notification);
        

    }


    


    // Step 2: Create User
    $user = User::create([
        'name' => $request->full_name,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'user_name' => $request->username,
    ]);

    
    // Step 3: If student, create student record
    if ($user->role == 3) {
        $student = $user->student()->create([
            'name' => $request->full_name,
            'gender' => strtolower($request->gender),
            'student_classes_id' => $request->class_id,


            
        ]);

        
    }

            //checking if admin is also updating his profile photo along with other data
            if( $request->hasFile('photo')){
    
                //save the request photo in a variable
                $file = $request->file('photo');
        
                //update the student profile image in the image folder directory, to avoid show previous image repeatedly
                @unlink(public_path('uploads/student_photos/'.$student->photo));
        
                //generating unique name for the image 
                $imageName = date('YmdHi'). '.' .$file->getClientOriginalName(); // sample-> 20250118.pic_name.png
        
                //move the photo to the uploads directory
                $file->move(public_path('uploads/student_photos'), $imageName);
        
                //save new admin profile image in the database
                $student['photo'] = $imageName;

                $student->save();
        
            }
            //save data
    $student->save();


    // Step 5: Redirect with notification
    $notification = [
        'message' => 'Student Added Successfully!',
        'alert-type' => 'success',
    ];

    return redirect()->back()->with($notification);
}


// manage student
     public function ManageStudent()
{
    $students = Student::orderBy('id', 'desc')->get();
    return view('backend.admin_backend.admin_create_student.manage_student', compact('students'));
}


    public function EditStudent($id){
        $students= Student::find($id);
        $classes = StudentClasses::all();
        return view('backend.admin_backend.admin_create_student.edit_student_view', compact('students', 'classes'));



    }// end method



        public function UpdateStudent(Request $request){

   
    $id = $request->id;
    $student = Student::find($id);

    // also fetch related user
    $user = $student->user;


    // ✅ VALIDATION
    $request->validate([
        // Student
        'full_name' => ['required','regex:/^[a-zA-Z0-9 ]+$/','max:255'],
        'gender' => ['required','in:male,female'],
        'username' => [
            'required',
            'unique:users,user_name,' .$student->user_id
        ],

        'password' => ['nullable',  'confirmed', 'min:6'],

        'class_id' => ['required','exists:student_classes,id'],
        // Image
        'photo' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
    ],
    // ✅ CUSTOM ERROR MESSAGES
    [
        'full_name.regex' => 'Full name can only contain letters, numbers and spaces.',
        'username.unique' => 'This username is already taken.',
        'photo.image' => 'Uploaded file must be an image.',
    
    ]);

    // update student info
    $student->name = $request->full_name;
    $student->gender = $request->gender;
    $student->student_classes_id = $request->class_id;
    // update photo if new one uploaded
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        @unlink(public_path('uploads/student_photos/'.$student->photo));
        $imageName = date('YmdHi').'.'.$file->getClientOriginalName();
        $file->move(public_path('uploads/student_photos'), $imageName);
        $student->photo = $imageName;
    }

     // ✅ update user table ( username + password)
     $user->user_name = $request->username;
     $user->name = $request->full_name;

     if (!empty($request->password)) {
         $user->password = Hash::make($request->password);
     }

     $user->save();

     $student->save();

    $notification = [
        'message' => 'Student Updated Successfully!',
        'alert-type' => 'success'
    ];

    return redirect()->route('manage.student')->with($notification);
}
// end method

    public function DeleteStudent($id){
        
        
        $student = Student::find($id);
        @unlink(public_path('uploads/student_photos/'.$student->photo));
        $student->Delete();

        if($student->user){
            $student->user->delete();
        }
        $student->delete();

        $notification = array(
            'message' => 'Student Deleted Successfully!',
            'alert-type' => 'info'
        );
    
        //redirect back to same page
     
        return redirect()->route('manage.student')->with($notification);


    }


        //ASSIGN STUDENT CLASS SUBJECT
   public function AssignStudentClassSubject(){

    $classes  = StudentClasses::all();

    // Fetch only current term & session set by admin
     // Current term & session
        $terms = Term::where('is_current', true)->get();
        $sessions = AcademicSession::where('is_current', true)->get();
    return view('backend.admin_backend.admin_create_student.assign_student_class_subject', compact('classes', 'terms', 'sessions'));
}



// Fetch subjects for selected class
public function FetchSubjects(Request $request)
{
    $class_id = $request->class_id;
    $class = StudentClasses::with('subjects')->where('id', $class_id)->first();
    $class_subjects = $class->subjects;

    $subject_data = [];
    $subject_data[] = '<input type="checkbox" id="select_all_subjects"> <label><strong>Select All Subjects</strong></label><br>';

    foreach ($class_subjects as $subject) {
        $subject_data[] =
            '<input class="form-check-input subject-checkbox" name="subject_ids[]" value="' . $subject->id . '" type="checkbox">
             <label>' . $subject->subject_name . '</label><br>';
    }

    return response()->json(['subjects' => $subject_data]);
}

// Fetch students for a class
public function FetchStudents(Request $request)
{
    $class_id = $request->class_id;
    $students = Student::where('student_classes_id', $class_id)
    ->orderBy('id', 'desc')->get();

    $student_data = [];
    $student_data[] = '<input type="checkbox" id="select_all_students"> <label><strong>Select All Students</strong></label><br>';

    foreach ($students as $student) {
        $student_data[] =
            '<input class="form-check-input student-checkbox" name="student_ids[]" value="' . $student->id . '" type="checkbox">
             <label>' . $student->name . '</label><br>';
    }

    return response()->json(['students' => $student_data]);
}




// Store Assignments
public function StoreStudentClassSubject(Request $request)
{
    $request->validate([
        'student_ids' => 'required|array',
        'class_id' => 'required',
        'subject_ids' => 'required|array',
        'session' => 'required|string',
        'term' => 'required|string'
    ]);

    foreach ($request->student_ids as $student_id) {
        foreach ($request->subject_ids as $subject_id) {
            $alreadyExist = AssignClassSubjectStudent::where('student_id', $student_id)
                ->where('student_classes_id', $request->class_id)
                ->where('subject_id', $subject_id)
                ->where('session', $request->session)
                ->where('term', $request->term)
                ->first();

            if (!$alreadyExist) {
                AssignClassSubjectStudent::create([
                    'student_id' => $student_id,
                    'subject_id' => $subject_id,
                    'student_classes_id' => $request->class_id,
                    'session' => $request->session,
                    'term' => $request->term,
                ]);
            }
        }
    }

    return redirect()->back()->with([
        'message' => 'Assigned Successfully',
        'alert-type' => 'success'
    ]);
}
// end method


public function ManageAssignStudentClassSubject(){

    $manageAssigns = AssignClassSubjectStudent::with(['student', 'subject', 'class'])
    ->orderBy('id', 'desc')
    ->get();


    return view('backend.admin_backend.admin_create_student.manage_assign_student_class_subject', compact('manageAssigns'));
  
}
// end method



public function EditAssignStudentClassSubject($id){
    $AssignSubjectstudent = AssignClassSubjectStudent::findOrFail($id);
    $students = Student::all();
    $subjects = Subject::all();
    $classes  = StudentClasses::all();

    return view('backend.admin_backend.admin_create_student.edit_assign_student_class_subject', compact('AssignSubjectstudent', 'students', 'subjects', 'classes'));

}//end method



public function UpdateAssignStudentClassSubject(Request $request){

    $id = $request->id;

    $request->validate([
        'student_id' => 'required',
        'class_id' => 'required',
        'subject_id' => 'required',
        'term' => 'required',
        'session' => 'required',
    ]);

    AssignClassSubjectStudent::findOrFail($id)->update([

        'student_id' => $request->student_id,
        'student_classes_id' => $request->class_id,
        'subject_id' => $request->subject_id,
        'term' => $request->term,
        'session' => $request->session,
    ]);

    $notification = array(
        'message' => 'Update Succesful',
        'alert-type' => 'info'
    );

    //redirect back to same page

    return redirect()->back()->with($notification);

}// end method


public function DeleteAssignStudentClassSubject($id){

    AssignClassSubjectStudent::findOrFail($id)->delete();
         $notification = array(
        'message' => ' Class Subject Assigned Deleted Succesfully',
        'alert-type' => 'info'
    );

    //redirect back to same page

    return redirect()->route('manage.assign.student.class.subject')->with($notification);


}

public function deleteAll()
{
     AssignClassSubjectStudent::query()->delete();

    return redirect()->back()->with([
        'message' => 'All assigned records deleted successfully',
        'alert-type' => 'success'
    ]);
}




   public function deleteAllStudent(){
    
    DB::transaction(function () {

        // Get all user IDs linked to teachers
        $studentUserIds = Student::pluck('user_id');

        // Delete teachers first or later (order doesn't matter inside transaction)
        Student::query()->delete();

        // Delete ONLY users linked to teachers
        User::whereIn('id', $studentUserIds)->delete();
    });

    return redirect()->back()->with([
        'message' => 'All students records deleted successfully',
        'alert-type' => 'success'
    ]);
}


}

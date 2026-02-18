<?php

namespace App\Http\Controllers\Admin;

use App\Models\Term;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\AssignedClassSubjectTeacher;

class TeacherController extends Controller
{
   public function AddTeacher(){
        return view('backend.admin_backend.admin_create_teacher.add_teachers');

    }// end method


public function StoreTeacher(Request $request){

     $request->validate([


        // User table
        'full_name' => ['required', 'regex:/^[a-zA-Z ]+$/'],
        'email'     => ['required', 'email:rfc,dns','unique:users,email'],
        'password'  => ['required', 'confirmed', 'min:8'],
        'role'      => ['required', 'in:2'],

        // Photo
        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

    ], [

        // ✅ CUSTOM ERROR MESSAGES
        'full_name.regex' => 'Full name must contain only letters and spaces.',
        'email.email' => 'Please enter a valid email address (example: name@example.com).',
        'email.dns' => 'Email domain does not exist.',
        'photo.image' => 'Uploaded file must be an image.',
        'password.confirmed' => 'Password confirmation does not match.',
        'email.unique' => 'This email is already taken.',
    ]);


        // Create User
    $user = User::create([
        'name' => $request->full_name,
        'password' => Hash::make($request->password),
        'email' => $request->email,
        'role' => $request->role,
        
    ]);



    // Step 3: If student, create student record
    if ($user->role == 2) {
        $teacher = $user->teacher()->create([
            'name' => $request->full_name,
      
        ]);

        
    }

            //checking if admin is also updating his profile photo along with other data
            if( $request->hasFile('photo')){
    
                //save the request photo in a variable
                $file = $request->file('photo');
        
                //update the admin profile image in the image folder directory, to avoid show previous image repeatedly
                @unlink(public_path('uploads/teachers_photos/'.$teacher->photo));
        
                //generating unique name for the image 
                $imageName = date('YmdHi'). '.' .$file->getClientOriginalName(); // sample-> 20250118.pic_name.png
        
                //move the photo to the uploads directory
                $file->move(public_path('uploads/teachers_photos'), $imageName);
        
                //save new admin profile image in the database
                $teacher['photo'] = $imageName;

                $teacher->save();
        
            }
            //save data
     $teacher->save();
 
     $notification = array(
         'message' => 'Teacher Added Successfully!',
         'alert-type' => 'success'
     );
 
     //redirect back to same page
  
     return redirect()->back()->with($notification);
 
 
     }//end method


    public function ManageTeacher(){
        $teachers = Teacher::orderBy('id', 'desc')->get();
        return view('backend.admin_backend.admin_create_teacher.manage_teacher', compact('teachers'));
    }//end method


    public function EditTeacher($id){
        $teachers = Teacher::find($id);
        return view('backend.admin_backend.admin_create_teacher.edit_teacher', compact('teachers'));

    }//end method


    public function UpdateTeacher(Request $request){
        $id = $request->id;
        $teacher = Teacher::find($id);

         // also fetch related user
    $user = $teacher->user;


     $request->validate([

    // ===== USER TABLE =====
    'full_name' => [
        'required',
        'regex:/^[a-zA-Z ]+$/'
    ],

    'email' => [
    'required',
    'email:rfc,dns',
    'unique:users,email,' . $teacher->user_id
],

    // Password optional on update
    'password' => [
        'nullable',
        'confirmed',
        'min:8'
    ],

    // ===== PHOTO =====
    'photo' => [
        'nullable',
        'image',
        'mimes:jpg,jpeg,png',
        'max:2048'
    ],

], [

    // ===== CUSTOM ERROR MESSAGES =====
    'full_name.regex' =>
        'Full name must contain only letters and spaces.',

        'email.email' => 'Please enter a valid email address (example: name@example.com).',

    'photo.image' =>
    'Uploaded file must be an image.',

    'photo.mimes' =>
    'Photo must be JPG, JPEG, or PNG.',

]);



        $teacher->name = $request->full_name;

        //checking if admin is also updating his profile photo along with other data
     if( $request->hasFile('photo')){
 
         //save the request photo in a variable
         $file = $request->file('photo');
         @unlink(public_path('uploads/teachers_photos/'.$teacher->photo));
         //generating unique name for the image 
         $imageName = date('YmdHi'). '.' .$file->getClientOriginalName(); // sample-> 20250118.pic_name.png
 
         //move the photo to the uploads directory
         $file->move(public_path('uploads/teachers_photos'), $imageName);
 
         //save new admin profile image in the database
         $teacher->photo= $imageName;
 
     }
    
    
     // ✅ update user table (email + username + password)
     $user->email = $request->email;
     $user->name = $request->full_name;

     if (!empty($request->password)) {
         $user->password = Hash::make($request->password);
     }

      //save data
     $user->save();
     $teacher->save();


 
 
     $notification = array(
         'message' => 'Teacher Updated Successfully!',
         'alert-type' => 'success'
     );
 
  
     return redirect()->route('manage.teacher')->with($notification);
 
 
     }//end Method


     public function DeleteTeacher($id){
        $teacher = Teacher::find($id);
        @unlink(public_path('uploads/teachers_photos/'.$teacher->photo));
        $teacher->delete();

        //delete teachers and the related user
        if($teacher->user){
            $teacher->user->delete();
        }
        $teacher->delete();

        
        $notification = array(
            'message' => 'Teacher Deleted Successfully!',
            'alert-type' => 'info'
        );
    
        //redirect back to same page
     
        return redirect()->route('manage.teacher')->with($notification);


    }

   public function deleteAllTeacher()
{
    DB::transaction(function () {

        // Get all user IDs linked to teachers
        $teacherUserIds = Teacher::pluck('user_id');

        // Delete teachers first or later (order doesn't matter inside transaction)
        Teacher::query()->delete();

        // Delete ONLY users linked to teachers
        User::whereIn('id', $teacherUserIds)->delete();
    });

    return redirect()->back()->with([
        'message' => 'All teachers records deleted successfully',
        'alert-type' => 'success'
    ]);
}


    public function AssignSubjectTeacher(){

        $terms = Term::where('is_current', true)->get();
        $sessions = AcademicSession::where('is_current', true)->get();
        $teachers = Teacher::all();
        $subjects = Subject::all();
        $classes  = StudentClasses::all();

        return view('backend.admin_backend.admin_create_teacher.assign_teacher_subject', compact('teachers', 'subjects', 'classes', 'terms', 'sessions'));

    }// end method

// Fetch subjects of a class from DB
public function FetchStudent(Request $request)
{
    $class_id = $request->class_id;

    $class = StudentClasses::with('subjects')->where('id', $class_id)->first();

    if (!$class) {
        return response()->json(['subjects' => '']);
    }

    $class_subjects = $class->subjects;
    $subject_data = '';

    foreach ($class_subjects as $subject) {
        $subject_data .= '
            <div>
                <input class="form-check-input" name="subject_ids[]" value="'.$subject->id.'" type="checkbox" id="subject_'.$subject->id.'">
                <label for="subject_'.$subject->id.'">'.$subject->subject_name.'</label>
            </div>
        ';
    }

    return response()->json(['subjects' => $subject_data]);

}// end method



// store subject teacher

    public function StoreAssignSubjectTeacher(Request $request){


        $AlreadyExist =  AssignedClassSubjectTeacher::where('teacher_id', $request->teacher_id)
        ->where('student_classes_id', $request->class_id)
        ->where('subject_id', $request->subject_ids)
        ->first();


        if($AlreadyExist){

                 $notification = array(
                'message' => ' Assign Subject Class Teacher Already exist',
                'alert-type' => 'info'
            );

            //redirect back to same page
  
      return redirect()->back()->with($notification);
            

        }else{
            
        $request->validate([

            'teacher_id' => 'required',
            'class_id' => 'required',
            'subject_ids' => 'required|array',
            'session' => 'required|string',
            'term' => 'required'
        ]);

        $sub_count = count($request->subject_ids);
        for($i=0; $i < $sub_count; $i++){

            AssignedClassSubjectTeacher::create([
                'teacher_id' => $request->teacher_id, 
                'subject_id' => $request->subject_ids[$i],
                 'student_classes_id' =>$request->class_id, 
                 'session' =>$request->session,
                 'term' => $request->term
    
            ]);
        }
       


        $notification = array(
            'message' => 'Subject Teacher Assigned Succesfully',
            'alert-type' => 'info'
        );
    
        //redirect back to same page
    
        return redirect()->back()->with($notification);
    
       }

    }
// end method



public function ViewAssignSubjectTeacher(){

    $manageAssigns = AssignedClassSubjectTeacher::with(['teacher', 'subject', 'class'])
    ->orderBy('id', 'desc')
    ->get();


    return view('backend.admin_backend.admin_create_teacher.manage_assign_subject_teacher', compact('manageAssigns'));
  
}
// end method



public function EditAssignSubjectTeacher($id){
    $AssignSubjectTeacher = AssignedClassSubjectTeacher::findOrFail($id);
    $teachers = Teacher::all();
    $subjects = Subject::all();
    $classes  = StudentClasses::all();

    return view('backend.admin_backend.admin_create_teacher.edit_assign_subject_teacher', compact('AssignSubjectTeacher', 'teachers', 'subjects', 'classes'));

}//end method


public function UpdateAssignSubjectTeacher(Request $request){

    $id = $request->id;

    $request->validate([
        'teacher_id' => 'required',
        'class_id' => 'required',
        'subject_id' => 'required',
        'term' => 'required',
        'session' => 'required',
    ]);

    AssignedClassSubjectTeacher::findOrFail($id)->update([

        'teacher_id' => $request->teacher_id,
        'student_classes_id' => $request->class_id,
        'subject_id' => $request->subject_id,
        'term' => $request->term,
        'session' => $request->session,
    ]);

    $notification = array(
        'message' => 'Subject Teacher Assigned Updated Succesfully',
        'alert-type' => 'info'
    );

    //redirect back to same page

    return redirect()->route('manage.assign.subject.teacher')->with($notification);

}// end method

public function DeleteAssignSubjectTeacher($id){
    AssignedClassSubjectTeacher::findOrFail($id)->delete();

         $notification = array(
        'message' => ' Subject Teacher Assigned Deleted Succesfully',
        'alert-type' => 'info'
    );

    //redirect back to same page

    return redirect()->route('manage.assign.subject.teacher')->with($notification);


}//end method




   public function deleteAllAssignedTeacher(){
    
    DB::transaction(function () {

        // Get all user IDs linked to teachers
        $teacherUserIds = AssignedClassSubjectTeacher::pluck('user_id');

        // Delete teachers first or later (order doesn't matter inside transaction)
        AssignedClassSubjectTeacher::query()->delete();

        // Delete ONLY users linked to teachers
        User::whereIn('id', $teacherUserIds)->delete();
    });

    return redirect()->back()->with([
        'message' => 'All teacher records deleted successfully',
        'alert-type' => 'success'
    ]);
}
//end method

}

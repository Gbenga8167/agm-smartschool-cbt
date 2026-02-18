<?php

namespace App\Http\Controllers\Teacher;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherAccountController extends Controller
{
     public function TeacherLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }//end method

    public function TeacherProfile(){

        $id = Auth::user()->id;
        $TeacherData = User::findOrFail($id);
        $teacherphoto = Teacher::where('user_id', $id)->first();
        return view('backend.teacher_backend.teacher_profile_view', compact('TeacherData', 'teacherphoto'));
    
    
    }//end method


    
    public function TeacherProfileUpdate(Request $request){

        $id = Auth::user()->id;
        $user = User::findOrFail($id);

        $teacher = Teacher::where('user_id', $id)->first();

        $request->validate([
           'user_name' => 'required',
           'email' => 'required|email',
           'photo' => 'nullable|image|max:2048',

        ]);

        $user->update([
            'user_name' => $request->user_name,
            'email' => $request->email,


        ]);
        

       

             //checking if admin is also updating his profile photo along with other data
        if( $request->hasFile('photo')){
    
            //save the request photo in a variable
            $file = $request->file('photo');
    
            //update the teacher profile image in the image folder directory, to avoid showing previous image repeatedly or dublicated image
            @unlink(public_path('uploads/teachers_photos/'.$teacher->photo));
    
            //generating unique name for the image 
            $imageName = date('YmdHi'). '.' .$file->getClientOriginalName(); // sample-> 20250118.pic_name.png
    
            //move the photo to the uploads directory
            $file->move(public_path('uploads/teachers_photos'), $imageName);
    
            //save new admin profile image in the database
            $teacher['photo'] = $imageName;

        }
      //save data
        $teacher->save();
    
        $notification = array(
            'message' => 'Teacher Profile Updated Successfully!',
            'alert-type' => 'success'
        );
    
        //redirect back to same page
    
        return redirect()->back()->with($notification);
    
    
    
    }//end method


    
    public function TeacherPasswordChange(){

        return view('backend.teacher_backend.teacher_password_change');
    
    }//end method


    public function TeacherPasswordUpdate(Request $request){

        $request->validate([
    
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
   
        // check if the users old password doesnt match the password in the database
        if(!Hash::check($request->old_password, Auth::user()->password)){
            $notification = array(
                'message' => 'Old Password Does Not Match!',
                'alert-type' => 'error');
      
            //redirect back to same page
        
            return redirect()->back()->with($notification);
        }

        //updating the new password with the old one
        User::whereId(Auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
    
        $notification = array(
            'message' => 'Password Updated Succesfully',
            'alert-type' => 'success'
        );
    
        //redirect back to same page
    
        return redirect()->back()->with($notification);


    }// end method
    

    // teacher assigned subject
    //view teacher assigned subject in the teacher's dashboard

    public function TeacherAssignedSubject()
{
    $teacher = auth()->user()->teacher; // Get the logged-in teacher  

    // Get current term & session
   // $currentTerm = DB::table('terms')->where('is_current', 1)->value('name');
   // $currentSession = DB::table('academic_sessions')->where('is_current', 1)->value('name');

    // Fetch only assignments for the current term & session
    $assignments = $teacher->assignedsubjectteacher()
        ->with(['subject', 'class'])
       // ->where('term', $currentTerm)
       // ->where('session', $currentSession)
        ->get();

    //return view('backend.teacher_backend.teacher_assigned_subject_view', compact('assignments', 'currentTerm', 'currentSession'));
     return view('backend.teacher_backend.teacher_assigned_subject_view', compact('assignments'));
}


}

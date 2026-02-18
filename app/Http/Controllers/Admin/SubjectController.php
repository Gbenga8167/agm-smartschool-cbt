<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\StudentClasses;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
      public function CreateSubject(){
        return view('backend.admin_backend.admin_create_subjects.create_subject');
    }// end method


    public function StotreSubject(Request $request){

        //validate user input
        $request->validate([
        'subject_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
        'status' => 'required',
    ], [
        'subject_name.regex' => 'Subject name can only contain letters, numbers, and spaces.',
    ]);
        //second method of inserting into database

        $AlreadyExist = Subject::where('subject_name', $request->subject_name)->first();
        if($AlreadyExist){

                 $notification = array(
                'message' => ' Subject Already exist',
                'alert-type' => 'info'
            );

            //redirect back to same page
  
      return redirect()->back()->with($notification);
            

        }else{

            Subject::create([
                'subject_name' => strtoupper($request->subject_name),
                'status' => $request->status
        
                 //end insert
               ]);
        
              
        
               $notification = array(
                'message' => 'Subject Created Succesfully',
                'alert-type' => 'info'
            );
        
            //redirect back to same page
        
            return redirect()->back()->with($notification);
        
        }
    }//end method


     public function ManageSubject(){

        $subjects = Subject::all();
        return view('backend.admin_backend.admin_create_subjects.manage_subject', compact('subjects'));

    }// end method


    public function EditSubject($id){

        $subject = Subject::find($id);
        return view('backend.admin_backend.admin_create_subjects.edit_subject', compact('subject'));

    }// end method





       public function UpdateSubject(Request $request){

        //validate user input
        $request->validate([
        'subject_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
        'status' => 'required',
    ], [
        'subject_name.regex' => 'Subject name can only contain letters, numbers, and spaces.',
    ]);
        $id = $request->id;
        Subject::find( $id)->update([
            'subject_name' => strtoupper($request->subject_name),
            'status' => $request->status
        ]);

        $notification = array(
            'message' => 'Subject Updated Succesfully',
            'alert-type' => 'info'
        );
    
        //redirect back to same page
    
        return redirect()->route('manage.subject')->with($notification);

    }//end method

    public function DeleteSubject($id){
        Subject::find( $id)->delete();

             $notification = array(
            'message' => ' Subject Deleted Succesfully',
            'alert-type' => 'info'
        );
    
        //redirect back to same page
    
        return redirect()->route('manage.subject')->with($notification);
    }// end method



     // Subject Combination All Method

     public function AddSubjectCombination(){
        $subjects = Subject::all();
        $classes = StudentClasses::all();

        return view('backend.admin_backend.admin_create_subjects.add_subject_combination', compact('subjects', 'classes'));
    }//end method



    
    public function StoreSubjectCombination(Request $request){


        $AlreadyExist = DB::table("student_class_subject")->where('student_classes_id', $request->class_id)->where('subject_id', $request->subject_ids)->first();
        if($AlreadyExist){
                $notification = array(
                'message' => ' Subject Combination Already exist',
                'alert-type' => 'info'
            );

            //redirect back to same page
  
      return redirect()->back()->with($notification);
            

        }else{

            $class = StudentClasses::find($request->class_id);
            $subject = $request->subject_ids;
           
           
            $class->subjects()->attach($subject);
  
            $notification = array(
          'message' => ' Combination Done Succesfully',
          'alert-type' => 'info'
      );
  
      //redirect back to same page
  
      return redirect()->back()->with($notification);
            
        }


        
      }//end method


      public function ManageSubjectCombination(){

        //creating a relationship btw classes_suject(pivot-table),subject tables and classes tables
        
                $results = DB::table('student_class_subject')
                           ->join('student_classes', 'student_class_subject.student_classes_id', 'student_classes.id')
                           ->join('subjects', 'student_class_subject.subject_id', 'subjects.id')
                           ->select(
                            'student_class_subject.*',
                            'student_classes.class_name',
                            'subjects.subject_name'
                           )
                           ->get();
        
                           return view('backend.admin_backend.admin_create_subjects.manage_subject_combination', compact('results'));
        
            }//end method



             public function DeleteSubjectCombination($id){
               DB::table("student_class_subject")->where('id', $id)->delete();

             $notification = array(
            'message' => ' Subject Deleted Succesfully',
            'alert-type' => 'info'
        );
    
        //redirect back to same page
    
        return redirect()->back()->with($notification);
    }// end method

}

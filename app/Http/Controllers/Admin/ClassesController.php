<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\StudentClasses;
use App\Http\Controllers\Controller;

class ClassesController extends Controller
{
     public function CreateClasses(){
        return view('backend.admin_backend.admin_create_classes.create_classes');
    }//end method

    public function StoreClasses(Request $request){

        //validate user input
 $request->validate([
        'class_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
        'status' => 'required',
    ], [
        'class_name.regex' => 'The class name field format is invalid.',
    ]);

//checking if class already exist in the database
        $AlreadyExist = StudentClasses::where('class_name', $request->class_name)->first();
        if( $AlreadyExist){
            $notification = array(
                'message' => ' Class Already exist',
                'alert-type' => 'info'
            );

            //redirect back to same page
  
      return redirect()->back()->with($notification);

        }else{
             //first method of inserting into database
             $class = new StudentClasses();
             $class->class_name = strtoupper($request->class_name);
             $class->status = $request->status;
             $class->save();
            //end insert

             $notification = array(
             'message' => 'Class Created Succesfully',
             'alert-type' => 'info'
 );

 //redirect back to same page

 return redirect()->back()->with($notification);
        }
    
      
    

    }//end method


    public function ManageClasses(){
        $classes = StudentClasses::all();
        return view('backend.admin_backend.admin_create_classes.manage_classes', compact('classes'));
    }// end method

  
    public function EditClass($id){
        $class = StudentClasses::Find($id);
        return view('backend.admin_backend.admin_create_classes.edit_classes', compact('class'));

    }// end method


    public function UpdateClass(Request $request){
        //validate user input
          $request->validate([
        'class_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
        'status' => 'required',
    ], [
        'class_name.regex' => 'The class name field format is invalid.',
    ]);

      $id = $request->id;
      StudentClasses::Find( $id )->update([
          'class_name' => strtoupper($request->class_name),
          'status' => $request->status
        
      ]);
      
      

      $notification = array(
          'message' => 'Student Class Updated Succesfully',
          'alert-type' => 'info'
      );
  
      //redirect back to same page
  
      return redirect()->route('manage.classes')->with($notification);
  

}// end method

public function DeleteClass($id){

    StudentClasses::Find($id)->delete();

    $notification = array(
        'message' => 'Student Class Deleted Succesfully',
        'alert-type' => 'info');

        return redirect()->back()->with($notification);

   }
        
}

@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">EDIT TEACHER INFO</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">EDIT Assigned</a></li>
                    <li class="breadcrumb-item active"> Class Teacher</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Edit - Assigned Subject Class Teacher </h4>
              
                <form action="{{route('update.assign.subject.teacher')}}" method="post">
                    @csrf

                    <input type="hidden" name="id" value="{{$AssignSubjectTeacher->id}}">

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Teacher</label>
                    <div class="col-sm-10">
                    <select  name="teacher_id" class="form-select" aria-label="Default select example">
                                                   
                         @foreach($teachers as $teacher)
                         <option {{$AssignSubjectTeacher->teacher_id == $teacher->id? 'selected' : ''}} value="{{$teacher->id}}">{{$teacher->name}}</option>
                         @endforeach
                         
                         </select>
                    </div>
                   
                </div>



                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Class</label>
                    <div class="col-sm-10">
                    <select  name="class_id" class="form-select" aria-label="Default select example" required>
                                                
                       @foreach($classes as $class)
                       <option {{$AssignSubjectTeacher->student_classes_id == $class->id? 'selected' : ''}} value="{{$class->id}}">{{$class->class_name}}</option>
                       @endforeach
                       
                       </select>
                    </div>
                   
                </div>



                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Subject</label>
                    <div class="col-sm-10">
                    <select  name="subject_id" class="form-select" aria-label="Default select example" required>

                        @foreach($subjects as $subject)
                        <option {{$AssignSubjectTeacher->subject_id == $subject->id? 'selected' : ''}} value="{{$subject->id}}">{{$subject->subject_name}}</option>
                        @endforeach
                                                    
                    </select>
                    </div>
                   
                </div>




                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Term</label>
                    <div class="col-sm-10">
                    <select  name="term" class="form-select" aria-label="Default select example" required>
                     
                    <option value="">-- Select Term --</option>
                    <option value="First Term" {{$AssignSubjectTeacher->term == 'First Term'? 'selected' : ''}}>First Term</option>
                    <option value="Second Term" {{$AssignSubjectTeacher->term == 'Second Term'? 'selected' : ''}}>Second Term</option>
                    <option value="Third Term" {{$AssignSubjectTeacher->term == 'Third Term'? 'selected' : ''}}>Third Term</option>
                                                    
                                                    
                    </select>
                    </div>
                   
                </div>


                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Session</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="session"  type="text" value="{{$AssignSubjectTeacher->session}}" required>
                       
                    </div>
                   
                </div>



           

                <!-- end row -->

                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Subject Teacher & Class</button>
                
                </form>
               
                

              
            </div>
        </div>
    </div> 
</div>
  


@endsection




@extends('backend.admin_backend.admin_dashboard')
@section('admin')

    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">ADD STUDENT INFO</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Add</a></li>
                    <li class="breadcrumb-item active"> Student</li>
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

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                <h4 class="card-title">Add - Student </h4>
              
                <form action="{{route('store.student')}}" method="post"  enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" value='3' name='role'>

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Fullname</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="full_name"  type="text" placeholder="Full Name" >
                     
                                            
                         @error('full_name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <!-- end row -->
                     
                   
                </div>




                 <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Class</label>
                     <div class="col-sm-10">
                         <select name="class_id" class="form-control">
                              <option value="">-- Select Class --</option>
                               @foreach($classes as $class)
                              <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                              @endforeach
                        </select>

                                               
                         @error('class_id')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                </div>




                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Gender</label>
                    <div class="col-sm-10">

                    <input class="form-check-input" type="radio" name="gender" checked="" value="Male">
                    <label class="form-check-label" for="formRadios1"> Male </label>


                    <input class="form-check-input" type="radio" name="gender" value="Female">
                    <label class="form-check-label" for="formRadios1"> Female </label>
                 
                                           
                         @error('gender')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>
                <!-- end row -->



                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">User name</label>
                    <div class="col-sm-10">
                        <input class="form-control"  name="username"  type="text" placeholder="User Name" >
                       

                                               
                         @error('username')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>

                <!-- end row -->


                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="password" type="password" placeholder="Password">
                   
                        
                                               
                         @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>
                <!-- end row -->




                 <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Confirm Password</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="password_confirmation" type="password" placeholder="Confirm_Password">
                   
                        
                                               
                         @error('password_confirmation')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>
                <!-- end row -->



                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Photo</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="photo" id="image" type="file">
                        

                                               
                         @error('photo')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>
                <!-- end row -->


                <div class="row mb-3">
                    <label for="example-email-input" class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                    <img id="ShowImage" src="{{asset('uploads/no_image.png')}}" alt="avatar-4" class="rounded avatar-md">
                    </div>
                </div>

                <!-- end row -->

                <button type="submit" class="btn btn-primary waves-effect waves-light">Add Student</button>
                
                </form>
               
                

              
            </div>
        </div>
    </div> 
</div>
  

<script>
  $(document).ready(function(){
 $('#image').on("change", function(e){
    var reader = new FileReader();
    reader.onload = function(e){
        $('#ShowImage').attr('src', e.target.result);
    }
    reader.readAsDataURL(e.target.files['0']);
 });

    });
</script>
  

@endsection




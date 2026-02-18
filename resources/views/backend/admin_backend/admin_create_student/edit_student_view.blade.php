@extends('backend.admin_backend.admin_dashboard')
@section('admin')

    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">UPDATE STUDENT INFO</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Update</a></li>
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
                <h4 class="card-title">Update - Student </h4>
              
                <form action="{{route('update.student')}}" method="post"  enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="id" value="{{$students->id}}">

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Fullname</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="full_name"  type="text" value="{{$students->name}}" >
                       

                         @error('full_name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>

            
                 <!-- Username -->
                 <div class="row mb-3">
                     <label for="username" class="col-sm-2 col-form-label">Username</label>
                     <div class="col-sm-10">
                         <input class="form-control" name="username" type="text" 
                                value="{{ $students->user->user_name }}">

                                 @error('username')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                     </div>
                 </div>
                 
            
                <!-- Password Field with Show/Hide -->
                <div class="row mb-3">
                    <label for="password" class="col-sm-2 col-form-label">Reset Password</label>
                    <div class="col-sm-10 position-relative">
                        <input class="form-control" name="password" id="password" type="password"
                               placeholder="Enter new password (leave blank to keep current)">
                
                        <!-- Toggle Eye Icon -->
                        <i class="fa fa-eye-slash" id="togglePassword"
                           style="position: absolute; right: 15px; top: 10px; cursor: pointer;"></i>
                
                        <small class="text-muted">Leave blank if you don't want to change the password.</small>
                    </div>
                </div>

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
                     <label for="class_id" class="col-sm-2 col-form-label" style="font-size:15px">Class</label>
                     <div class="col-sm-10">
                         <select name="class_id" class="form-select">
                             <option value="">-- Select Class --</option>
                             @foreach($classes as $class)
                                 <option value="{{ $class->id }}" 
                                     {{ $students->student_classes_id == $class->id ? 'selected' : '' }}>
                                     {{ $class->class_name }}
                                 </option>
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

                    <input class="form-check-input" type="radio" name="gender"  value="male" {{$students->gender == 'male'? 'checked' : ''}} >
                    <label class="form-check-label" for="formRadios1"> Male </label>


                    <input class="form-check-input" type="radio" name="gender" value="female" {{$students->gender == 'female'? 'checked' : ''}}>
                    <label class="form-check-label" for="formRadios1"> Female </label>
                 
                     @error('gender')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>



                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Photo</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="photo" id="image"  type="file">
                        
                         @error('photo')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>

                


                <div class="row mb-3">
                    <label for="example-email-input" class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                    <img id="ShowImage" src="{{ empty($students->photo)? asset('uploads/no_image.png') : asset('uploads/student_photos/'.$students->photo)}}" alt="avatar-4" class="rounded avatar-md">
                    </div>
                </div>


                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Student</button>
                
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
  
<script>
    // Password Show/Hide
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>

@endsection
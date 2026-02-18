@extends('backend.admin_backend.admin_dashboard')
@section('admin')


    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">UPDATE TEACHER INFO</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Update</a></li>
                    <li class="breadcrumb-item active"> Teacher</li>
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


                <h4 class="card-title">Update - Teacher </h4>
              
                <form action="{{route('update.teacher')}}" method="post"  enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="id" value="{{$teachers->id}}">

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Fullname</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="full_name"  type="text" value="{{$teachers->name}}" >
                        
                         @error('full_name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>

                <!-- Email address -->
                <div class="row mb-3">
                     <label for="email" class="col-sm-2 col-form-label">Email</label>
                     <div class="col-sm-10">
                         <input class="form-control" name="email" type="text" 
                                value="{{ $teachers->user->email }}" required>

                         @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                     </div>
                 </div>
                 
            
                <!-- Password Field with Show/Hide -->
                <div class="row mb-3">
                    <label for="password" class="col-sm-2 col-form-label">Reset Password </label>
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
                    <img id="ShowImage" src="{{ empty($teachers->photo)? asset('uploads/no_image.png') : asset('uploads/teachers_photos/'.$teachers->photo)}}" alt="avatar-4" class="rounded avatar-md">
                    </div>
                </div>


                

                <!-- end row -->

                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Teacher</button>
                
                </form>
               
                

              
            </div>
        </div>
    </div> 
</div>
  

<script>
    //image update
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




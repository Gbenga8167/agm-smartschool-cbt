@extends('backend.admin_backend.admin_dashboard')
@section('admin')


    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">ADD Teacher INFO</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Add</a></li>
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



                <h4 class="card-title">Add - New Teacher </h4>
              
                <form action="{{route('store.teacher')}}" method="post"  enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" value='2' name='role'>

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Fullname</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="full_name" value="{{ old('full_name') }}" type="text" placeholder="Full Name" >
                       
                         @error('full_name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   

                   
                </div>



                
                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="email" type="text" placeholder="Email Address" value="{{ old('email') }}">
                        
                         @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>
            

                <!-- end row -->

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="password" type="password" placeholder="Password" value="{{ old('password') }}">
                        
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
                        <input class="form-control" name="photo" id="image" type="file" value="{{ old('photo') }}">
                        
                         @error('photo')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                   
                </div>



                <div class="row mb-3">
                    <label for="example-email-input" class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-10">
                    <img id="ShowImage" src="{{asset('uploads/no_image.png')}}" alt="avatar-4" class="rounded avatar-md">
                    </div>
                </div>
                <!-- end row -->



                <button type="submit" class="btn btn-primary waves-effect waves-light">Add Teacher</button>
                
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




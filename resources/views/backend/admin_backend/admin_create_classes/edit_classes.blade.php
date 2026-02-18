@extends('backend.admin_backend.admin_dashboard')
@section('admin')

    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">EDIT CLASSES</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Edit</a></li>
                    <li class="breadcrumb-item active"> Classes</li>
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

                <h4 class="card-title">Edit - Classes </h4>
              
                <form action="{{route('update.class')}}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$class->id}}">

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Classes</label>
                    <div class="col-sm-10">
                        <input class="form-control  @error('class_name') is-invalid @enderror" name="class_name"  type="text" value="{{$class->class_name}}" >
                      
                        @error('class_name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    </div>
                   
                </div>

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                    <select  name="status" required class="form-select" aria-label="Default select example">
                                                    <option value="1">Active</option>
                                                    <option value="0">In-Active</option>
                                                    </select>
                    </div>
                   
                </div>

                <!-- end row -->

                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Class</button>
                
                </form>
               
                

              
            </div>
        </div>
    </div> 
</div>
  



@endsection




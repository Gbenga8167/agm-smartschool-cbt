@extends('backend.admin_backend.admin_dashboard')
@section('admin')
<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">MANAGE STUDENTS </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Manage</a></li>
                    <li class="breadcrumb-item active"> Students</li>
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
        
                                        <h4 class="card-title">View Students Info</h4>

        
                                        @if(!$students->isEmpty())

                                         <div class="d-flex justify-content-end mb-3">
                                             <form action="{{ route('delete.all.student') }}"
                                                   method="POST"
                                                   class="delete-all-form">
                                                 @csrf
                                                 @method('DELETE')

                                                 <button type="submit" class="btn btn-danger">
                                                     <i class="bi bi-trash3"></i> Delete All
                                                 </button>
                                             </form>
                                         </div>

                                         @endif

                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Photo</th>
                                                <th>Students Name</th>
                                                <th>Created_at</th>
                                               
                                                <th>Action</th>
                                            
                                            </tr>
                                            </thead>
        
        
                                            <tbody>
                                                @foreach($students as $key => $student)

                                                <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>
                                               
                                                    <img src="{{ empty($student->photo)? asset('uploads/no_image.png') : asset('uploads/student_photos/'.$student->photo)}}" alt="avatar-4" class="rounded avatar-md" >
                                               </td>
                                                <td>{{ ucwords(strtolower($student->name)) }}</td>
                                                <td>{{ $student->created_at}}</td>
                                            

                                                <td > <a href="{{route('edit.student', $student->id)}}">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">Edit</button>
                                                </a> 


                                                 <form action="{{route('delete.student', $student->id)}}" method="POST" class="d-inline delete-form" >
                                                       @csrf
                                                       @method('DELETE')
                                                       <button type="submit" class="btn btn-danger waves-effect waves-light" >
                                                           Delete
                                                       </button>
                                                   </form>
                                            </td>
                                               
                                            </tr>
                                            

                                                @endforeach

                                            </tr>
                                            </tbody>
                                        </table>
        
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->



                        

<script>
    // JS (SweetAlert only submits form)
    $(document).on('submit', '.delete-form', function(e){
    e.preventDefault();
    let form = this;

    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

</script>
<script>
    /* MASS POP-UP DELETE ALL 
$(document).on('submit', '.delete-all-form', function(e){
    e.preventDefault();
    let form = this;

    Swal.fire({
        title: 'Delete ALL records?',
        text: "This will delete ALL teachers record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete all!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

*/
</script>



<script>

// DELETE ALL TEXT FOR SWEET ALERT TYPING DELETE BEFORE DATA DELETION
$(document).on('submit', '.delete-all-form', function(e){
    e.preventDefault();
    let form = this;
    Swal.fire({
    title: 'Type DELETE to confirm',
    input: 'text',
    showCancelButton: true,
    preConfirm: (value) => {
        if (value !== 'DELETE') {
            Swal.showValidationMessage('You must type DELETE');
        }
        return value;
    }
}).then((result) => {
    if (result.isConfirmed) {
        form.submit();
    }
});
});

</script>



@endsection
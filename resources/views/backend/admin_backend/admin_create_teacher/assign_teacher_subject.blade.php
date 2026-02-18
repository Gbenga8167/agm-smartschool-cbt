@extends('backend.admin_backend.admin_dashboard')
@section('admin')

    <div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">ASSIGN TEACHER SUBJECT & CLASS</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Assign Subject</a></li>
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

                <h4 class="card-title">Assign Subject - Teacher </h4>
              
                <form action="{{route('store.teacher.subject')}}" method="post">
                    @csrf 

                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Teacher</label>
                    <div class="col-sm-10">
                    <select  name="teacher_id" required class="form-select" aria-label="Default select example">
                         <option selected value="">--Select Teacher--</option>

                         @foreach($teachers as $teacher)
                        <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                         @endforeach
                                                    
                        </select>
                    </div>
                   
                </div>
                     <!-- end row -->


                <div class="row mb-3">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Class</label>
                    <div class="col-sm-10">
                    <select  name="class_id" required  aria-label="Default select example" class="form-select dynamic" data-dependant="student">
                         <option selected value="">-- Select Class --</option>

                         @foreach($classes as $class)
                        <option value="{{$class->id}}">{{$class->class_name}}</option>
                         @endforeach
                                                    
                        </select>
                    </div>
                   
                </div> 
                   <!-- end row -->




                 <div class="row mb-3 showSubject">
                    <label for="example-text-input" class="col-sm-2 col-form-label" style="font-size:15px">Subject</label>
                  <div class="col-sm-10 sub">
   
                   </div>
                </div>
          <!-- end row -->
                

                
                                  <!-- Term -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Term</label>
                            <div class="col-sm-10">
                                <select name="term" class="form-select" required>
                                     <option selected value="">--Select Term--</option>
                                    @foreach($terms as $term)
                                       <option value="{{$term->name}}">{{$term->name}}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Session -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Academic Session</label>
                            <div class="col-sm-10">
                                <select name="session" class="form-select" required>
                                     <option selected value="">--Select Session--</option>
                                    @foreach($sessions as $session)
                                      <option value="{{$session->name}}">{{$session->name}}</option>
                                      @endforeach
                                </select>
                            </div>
                        </div>

                <!-- end row -->

                <button type="submit" class="btn btn-primary waves-effect waves-light">Assign </button>
                
                </form>
               
                

              
            </div>
        </div>
    </div> 
</div>
  

<script>
$(document).ready(function(){
    $('.showSubject').hide();

    $('.dynamic').on('change', function(){
        let class_id = $(this).val();
        let _token = "{{ csrf_token() }}";

        $.ajax({
            url:"{{route('fetch.student')}}",
            method:"GET",
            data:{ class_id:class_id, _token:_token },
            success:function(result){
                // Add "Select All" checkbox
                let selectAll = `
                    <div>
                        <input type="checkbox" id="select_all_subjects" class="form-check-input">
                        <label for="select_all_subjects"><strong>Select All Subjects</strong></label>
                    </div><hr>
                `;

                $('.sub').html(selectAll + result.subjects);
                $('.showSubject').show();

                // Handle "Select All"
                $('#select_all_subjects').on('change', function(){
                    $('input[name="subject_ids[]"]').prop('checked', this.checked);
                });
            }
        });
    });
});

</script>
  

@endsection




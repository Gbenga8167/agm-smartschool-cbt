@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title">Assign Student Class - Subject</h4>

                    <form action="{{ route('store.student.class.subject') }}" method="post">
                        @csrf 

                        <!-- Class Buttons -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Select Students by Class:</strong></label><br>
                            @foreach($classes as $class)
                                <button type="button" class="btn btn-outline-primary btn-sm m-1 fetch-students" data-class="{{ $class->id }}">
                                    {{ $class->class_name }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Student Checkboxes -->
                        <div class="row mb-3 showStudents" style="display:none;">
                            <label class="col-sm-2 col-form-label">Students</label>
                            <div class="col-sm-10 students-list"></div>
                        </div>

                        <!-- Class Dropdown -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Class</label>
                            <div class="col-sm-10">
                                <select name="class_id" class="form-select class-select" required>
                                    <option value="">-- Select Class --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Subjects -->
                        <div class="row mb-3 showSubject" style="display:none;">
                            <label class="col-sm-2 col-form-label">Subjects</label>
                            <div class="col-sm-10 subjects-list"></div>
                        </div>

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

                        <button type="submit" class="btn btn-primary">Assign</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

    // Fetch Students when class button clicked
    $('.fetch-students').on('click', function(){
        let class_id = $(this).data('class');
        $.get("{{ route('fetch.students') }}", { class_id: class_id }, function(result){
            $('.students-list').html(result.students.join('')); // ✅ join array into HTML
            $('.showStudents').show();
        });
    });

    // Fetch Subjects when class dropdown changes
    $('.class-select').on('change', function(){
        let class_id = $(this).val();
        $.get("{{ route('fetch.subjects') }}", { class_id: class_id }, function(result){
            $('.subjects-list').html(result.subjects.join('')); // ✅ join array into HTML
            $('.showSubject').show();
        });
    });

    // Select All Students
    $(document).on('change', '#select_all_students', function(){
        $('.student-checkbox').prop('checked', this.checked);
    });

    // Select All Subjects
    $(document).on('change', '#select_all_subjects', function(){
        $('.subject-checkbox').prop('checked', this.checked);
    });

});
</script>

@endsection

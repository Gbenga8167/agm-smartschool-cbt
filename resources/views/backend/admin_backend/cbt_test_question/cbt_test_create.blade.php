@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">CBT TEST</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">Create</li>
                    <li class="breadcrumb-item active">CBT Tests</li>
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

            @if(session('message'))
                <div class="alert alert-{{ session('alert-type') }}">
                    {{ session('message') }}
                </div>
            @endif

            
            <h4 class="card-title">Create CBT Test (Admin)</h4>

            <form action="{{ route('admin.cbt.test.store') }}" method="POST">
                @csrf

                <!-- Instructions -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Instructions</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title"
                               value="{{ old('title') }}" placeholder="Instructions">
                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Class -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Class</label>
                    <div class="col-sm-10">
                        <select name="class_id" class="form-select">
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Subject -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Subject</label>
                    <div class="col-sm-10">
                        <select name="subject_id" class="form-select">
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Teacher -->
                <div class="row mb-3">
                                       <label class="col-sm-2 col-form-label">Teacher</label>
                    <div class="col-sm-10">
                        <select name="teacher_id" id="teacher_id" class="form-select">
                            <option value="">-- Select Teacher --</option>
                        </select>
                        @error('teacher_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Term -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Term</label>
                    <div class="col-sm-10">
                        <select name="term" class="form-select">
                            <option value="">-- Select Term --</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->name }}">{{ $term->name }}</option>
                            @endforeach
                        </select>
                        @error('term')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Academic Session -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Academic Session</label>
                    <div class="col-sm-10">
                        <select name="session" class="form-select">
                            <option value="">-- Select Session --</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->name }}">{{ $session->name }}</option>
                            @endforeach
                        </select>
                        @error('session')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Duration -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Duration (minutes)</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="duration_minutes"
                               value="{{ old('duration_minutes') }}">
                        @error('duration_minutes')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Assessment Type -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Assessment Type</label>
                    <div class="col-sm-10">
                        <select name="assessment_type" class="form-select">
                            <option value="">-- Select Assessment Type --</option>
                            <option value="1st Test">1st Test</option>
                            <option value="2nd Test">2nd Test</option>
                            <option value="3rd Test">3rd Test</option>
                            <option value="Exam">Exam</option>
                        </select>
                        @error('assessment_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Start Time -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Start Time</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" class="form-control" name="start_time"
                               value="{{ old('start_time') }}">
                        @error('start_time')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- End Time -->
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">End Time (optional)</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" class="form-control" name="end_time"
                               value="{{ old('end_time') }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Create CBT Test
                </button>

            </form>

            </div>
        </div>
    </div>
</div>

</div>




<script>
document.addEventListener('DOMContentLoaded', function () {

    const classSelect = document.querySelector('select[name="class_id"]');
    const subjectSelect = document.querySelector('select[name="subject_id"]');
    const teacherSelect = document.getElementById('teacher_id');

    function loadTeachers() {
        const classId = classSelect.value;
        const subjectId = subjectSelect.value;

        if (!classId || !subjectId) {
            teacherSelect.innerHTML = '<option value="">-- Select Teacher --</option>';
            return;
        }

        fetch(`/admin/get-teachers/${classId}/${subjectId}`)
            .then(response => response.json())
            .then(data => {
                teacherSelect.innerHTML = '<option value="">-- Select Teacher --</option>';

                if (data.length === 0) {
                    teacherSelect.innerHTML +=
                        `<option value="">No teacher assigned</option>`;
                }

                data.forEach(teacher => {
                    teacherSelect.innerHTML +=
                        `<option value="${teacher.id}">${teacher.name}</option>`;
                });
            });
    }

    classSelect.addEventListener('change', loadTeachers);
    subjectSelect.addEventListener('change', loadTeachers);
});
</script>

@endsection

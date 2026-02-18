@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container my-4">

    <h4 class="text-center mb-4">
        📊 Admin CBT Results
    </h4>

    {{-- ================= SUCCESS / ERROR MESSAGE ================= --}}
    @if(session('message'))
        <div class="alert alert-{{ session('alert-type') == 'error' ? 'danger' : 'success' }}">
            {{ session('message') }}
        </div>
    @endif

    {{-- ================= FILTER FORM ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold bg-light">
            🔍 Filter Results
        </div>

        <div class="card-body">
            <form action="{{ route('cbt.results.fetch') }}" method="POST">
                @csrf

                {{-- Class --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Class</label>
                    <select name="class_id" id="class_id" class="form-select" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ isset($selectedClass) && $selectedClass == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Subject --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Subject</label>
                    <select name="subject_id" id="subject_id" class="form-select" required>
                        <option value="">-- Select Subject --</option>

                        @if(isset($selectedSubject) && isset($selectedClass))
                            @php
                                $subjects = \App\Models\Subject::whereHas('assignedTeachers', function($q) use ($selectedClass){
                                    $q->where('student_classes_id', $selectedClass);
                                })->get();
                            @endphp

                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}"
                                    {{ $selectedSubject == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->subject_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('subject_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-info">
                    Fetch Results
                </button>
            </form>
        </div>
    </div>

    {{-- ================= RESULTS TABLE ================= --}}
    @if(isset($results))

        @if($results->isNotEmpty())

            <div class="alert alert-info">
                <strong>Selected Class:</strong>
                {{ optional($results->first()->test->class)->class_name }} <br>

                <strong>Selected Subject:</strong>
                {{ optional($results->first()->test->subject)->subject_name }}
            </div>

            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Assessment Type</th>
                                <th>Score</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($results as $key => $result)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ ucwords(strtolower($result->student->name)) }}</td>
                                <td>{{ strtoupper($result->test->class->class_name) }}</td>
                                <td>{{ ucfirst($result->test->assessment_type) }}</td>
                                <td class="fw-bold">{{ $result->score }}</td>
                                <td>
                                    <form id="retake-form-{{ $result->id }}"
                                          action="{{ route('cbt.retake', $result->id) }}"
                                          method="POST">
                                        @csrf

                                        <button type="button"
                                                class="btn btn-warning btn-sm"
                                                onclick="confirmRetake({{ $result->id }})">
                                            🔄 Retake
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        @else
            <div class="alert alert-warning mt-3">
                No results found for selected class and subject.
            </div>
        @endif

    @endif

</div>

{{-- ================= SCRIPTS ================= --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

// ================= LOAD SUBJECTS DYNAMICALLY =================
$('#class_id').change(function () {

    let classId = $(this).val();
    let subjectSelect = $('#subject_id');

    subjectSelect.html('<option value="">Loading subjects...</option>');

    if (classId) {

        $.get('/admin/cbt-results/subjects/' + classId, function (data) {

            subjectSelect.html('<option value="">-- Select Subject --</option>');

            if (data.length === 0) {
                subjectSelect.html('<option value="">No subjects assigned to this class</option>');
                return;
            }

            data.forEach(function(subject){
                subjectSelect.append(
                    `<option value="${subject.id}">${subject.subject_name}</option>`
                );
            });

        });

    } else {
        subjectSelect.html('<option value="">-- Select Subject --</option>');
    }

});


// ================= RETAKE CONFIRMATION =================
function confirmRetake(attemptId) {

    Swal.fire({
        title: 'Allow Retake?',
        text: "This will delete the student's attempt and answers.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, allow retake!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('retake-form-' + attemptId).submit();
        }
    });

}

</script>

@endsection

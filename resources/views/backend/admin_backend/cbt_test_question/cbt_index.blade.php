@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">ADD CBT QUESTIONS</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Add</a></li>
                        <li class="breadcrumb-item active">CBT Questions</li>
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
                    <h4 class="card-title">My CBT Tests</h4>

                    <!-- CLASS SELECTION FORM -->
                    <form method="GET" action="{{ route('admin.cbt.tests.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->class_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-primary">
                                    View CBT Tests
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- ALERTS -->
                    @if(!request()->filled('class_id'))
                        <div class="alert alert-info">
                            Select a class to view CBT tests.
                        </div>
                    @elseif($cbtTests->isEmpty())
                        <div class="alert alert-info">
                            No CBT test available for this class yet.
                        </div>
                    @endif

                    <!-- CBT TEST TABLE -->
                    @if($cbtTests->isNotEmpty())
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr class="table-center">
                                    <th><b>S/N</b></th>
                                    <th><b>Class</b></th>
                                    <th><b>Subject</b></th>
                                    <th><b>Instruction</b></th>
                                    <th><b>Term</b></th>
                                    <th><b>Session</b></th>
                                    <th><b>Duration(min)</b></th>
                                    <th><b>Type</b></th>
                                    <th><b>Action</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cbtTests as $key => $test)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$test->class->class_name}}</td>
                                        <td>{{$test->subject->subject_name}}</td>
                                        <td>{{$test->title}}</td>
                                        <td>{{$test->term}}</td>
                                        <td>{{$test->session}}</td>
                                        <td align="center">{{$test->duration_minutes}}</td>
                                        <td>{{$test->assessment_type}}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="{{ route('admin.cbt.test.edit', $test->id) }}" class="btn btn-success">Edit Test</a>
                                                <a href="{{ route('admin.cbt.questions.create', $test->id) }}" class="btn btn-success">Add Questions</a>
                                                <a href="{{ route('admin.cbt.questions.edit', $test->id) }}" class="btn btn-warning">Edit Questions</a>

                                                <form action="{{ route('admin.cbt.questions.delete.all', $test->id) }}"
                                                      method="POST"
                                                      class="delete-all-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete All</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

</div>

<script>
    // MASS POP-UP DELETE ALL 
    $(document).on('submit', '.delete-all-form', function(e){
        e.preventDefault();
        let form = this;

        Swal.fire({
            title: 'Delete ALL records?',
            text: "This will delete all CBT tests for the selected subject and class!",
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
</script>

@endsection

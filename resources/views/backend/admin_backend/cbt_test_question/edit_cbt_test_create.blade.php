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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Create</a></li>
                        <li class="breadcrumb-item active">Edit CBT Test</li>
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

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h4 class="card-title">Edit CBT Test</h4>
                    <form action="{{ route('admin.cbt.test.update', $cbtTest->id) }}" method="post">
                        @csrf

                        <!-- Instructions / Title -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Instructions</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" value="{{ old('title', $cbtTest->title) }}">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Class -->
                        <div class="row mb-3" style="display:none">
                            <label class="col-sm-2 col-form-label">Class</label>
                            <div class="col-sm-10">
                                <select name="class_id" class="form-select">
                                    <option value="">--Select Class--</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $cbtTest->student_classes_id == $class->id ? 'selected' : '' }}>
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
                        <div class="row mb-3" style="display:none">
                            <label class="col-sm-2 col-form-label">Subject</label>
                            <div class="col-sm-10">
                                <select name="subject_id" class="form-select">
                                    <option value="">--Select Subject--</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $cbtTest->subject_id == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Term -->
                        <div class="row mb-3" style="display:none">
                            <label class="col-sm-2 col-form-label">Term</label>
                           <div class="col-sm-10">
                             <input type="text" class="form-control" value="{{ $terms->name ?? 'No Current Term Set' }}" readonly>

                              <input type="hidden" name="term" value="{{ $terms->name ?? '' }}">

                            </div> 
                        </div>
                        <!-- Academic Session -->
                        <div class="row mb-3" style="display:none">
                            <label class="col-sm-2 col-form-label">Academic Session</label>
                            <div class="col-sm-10">
                        
                                <input type="text" class="form-control"
                                       value="{{ $session->name ?? 'No Current Session Set' }}"
                                       readonly>
                        
                                <input type="hidden" name="session"
                                       value="{{ $session->name ?? '' }}">
                        
                                @error('session')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>


                        <!-- Duration -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Duration (minutes)</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="duration_minutes" value="{{ old('duration_minutes', $cbtTest->duration_minutes) }}">
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
                                    <option value="">--Assessment Type--</option>
                                    <option value="1st Test" {{ $cbtTest->assessment_type == '1st Test' ? 'selected' : '' }}>1st Test</option>
                                    <option value="2nd Test" {{ $cbtTest->assessment_type == '2nd Test' ? 'selected' : '' }}>2nd Test</option>
                                    <option value="3rd Test" {{ $cbtTest->assessment_type == '3rd Test' ? 'selected' : '' }}>3rd Test</option>
                                    <option value="Exam" {{ $cbtTest->assessment_type == 'Exam' ? 'selected' : '' }}>Exam</option>
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
                                <input type="datetime-local" class="form-control" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($cbtTest->start_time)->format('Y-m-d\TH:i')) }}">
                                @error('start_time')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- End Time -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">End Time (optional)</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" class="form-control" name="end_time" value="{{ optional($cbtTest->end_time)->format('Y-m-d\TH:i') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            Update CBT Test
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

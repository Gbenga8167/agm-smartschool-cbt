@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded shadow-sm bg-primary text-white">
        <h3 class="fw-bold mb-0 text-white">
            <i class="bi bi-gear-fill me-2 "></i> Academic Settings
        </h3>
        <span class="badge bg-light text-primary p-2 px-3 shadow-sm">Manage Sessions & Terms</span>
    </div>

    {{-- Flash Message --}}
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="row">
        {{-- Sessions --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5>Academic Sessions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academic.session.store') }}" method="POST" class="mb-3">
                        @csrf

                        <!--
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="e.g. 2024/2025" required>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                         -->
                    </form>

                    {{-- Responsive Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Session</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                    <tr>
                                        <td>{{ $session->name }}</td>
                                        <td>
                                            @if($session->is_current)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="d-flex flex-wrap gap-1">
                                            {{-- Toggle --}}
                                           <!-- <form action="{{ route('academic.session.toggle', $session->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    {{ $session->is_current ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            -->

                                            {{-- Edit --}}
                                            <a href="{{ route('academic.session.edit', $session->id) }}" class="btn btn-sm btn-info">
                                                Edit
                                            </a>

                                            {{-- Delete --}}
                                           <!-- <form action="{{ route('academic.session.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this session?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form> -->
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

               <!-- Pagination -->
<div class="d-flex justify-content-center mt-3">
    {{ $sessions->links('pagination::bootstrap-5') }}
</div>


                </div>
            </div>
        </div>

        {{-- Terms --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5>Academic Terms</h5>
                </div>
                <div class="card-body">
                    {{-- Responsive Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Term</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($terms as $term)
                                    <tr>
                                        <td>{{ $term->name }}</td>
                                        <td>
                                            @if($term->is_current)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('academic.term.toggle', $term->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    {{ $term->is_current ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

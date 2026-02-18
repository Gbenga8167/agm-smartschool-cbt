@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container mt-4">
    <h3>Edit Academic Session</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


        {{-- Flash Message --}}
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif



    <form action="{{ route('academic.session.update', $session->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Session Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $session->name }}" required>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{route('session.create')}}" class="btn btn-secondary">Back</a>
    </form>
</div>

@endsection

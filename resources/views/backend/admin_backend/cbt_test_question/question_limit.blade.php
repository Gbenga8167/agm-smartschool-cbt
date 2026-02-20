@extends('backend.admin_backend.admin_dashboard')
@section('admin')


<style>
    /* Remove arrows in Chrome, Safari, Edge */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Remove arrows in Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>



<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4">

                <div class="card-header text-white text-center py-3 rounded-top-4"
                     style="background: linear-gradient(135deg, #0f9b8e, #00c9a7);">
                    <h5 class="mb-0 fw-bold">
                        ⚙ Question Limit Settings
                    </h5>
                </div>

                <div class="card-body p-4">


        
                   @if(session('message'))
                         <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                             {{ session('message') }}
                             <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                         </div>
                     @endif
                     
                    <form method="POST" action="{{ route('admin.question.limit.update') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Current Question Limit
                            </label>

                            <input type="number"
                                   name="test_limit"
                                   value="{{ $session->test_limit }}"
                                   class="form-control form-control-lg text-center shadow-sm"
                                   min="1"
                                   required>

                            <small class="text-muted">
                                This determines how many questions each student will see per test.
                            </small>
                        </div>

                        <div class="d-grid">
                            <button type="submit"
                                    class="btn btn-lg text-white fw-bold"
                                    style="background: linear-gradient(135deg, #134e5e, #71b280); border: none;">
                                Update Limit
                            </button>
                        </div>

                    </form>

                </div>

                <div class="card-footer text-center text-muted small py-3">
                    Changing this will affect only new test attempts.
                </div>

            </div>

        </div>
    </div>

</div>

@endsection
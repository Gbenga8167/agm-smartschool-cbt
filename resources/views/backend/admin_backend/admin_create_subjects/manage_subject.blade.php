@extends('backend.admin_backend.admin_dashboard')
@section('admin')



<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">MANAGE STUDENT SUBJECT</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Manage</a></li>
                    <li class="breadcrumb-item active"> Subject</li>
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
        
                                        <h4 class="card-title">View Subject Info</h4>

        
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Subject Name</th>
                                                <th>Creation Date</th>
                                                <th>Status</th> 
                                                <th>Action</th>
                                            
                                            </tr>
                                            </thead>
        
        
                                            <tbody>
                                                @foreach($subjects as $key => $subject)

                                                <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ strtoupper($subject->subject_name) }}</td>
                                                 <td>{{ $subject->created_at }}</td>
                                                <td>
                                                   @if($subject->status == 1)
                                                   <span class="badge bg-success" style="padding:10px 15px; font-size:15px">Active</span>
                                                    @else <span class="badge bg-danger" style="padding:10px 8px; font-size:15px">In-Active</span>
                                                    @endif</td>
                                                </td>


                                                <td > <a href="{{ route('edit.subject',$subject->id)}}">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">Edit</button>
                                                </a> 

                                                
                                                <a href="{{ route('delete.subject',$subject->id)}}" id="delete">
                                                <button type="submit"  class="btn btn-danger waves-effect waves-light">Delete</button>
                                                </a>

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





@endsection
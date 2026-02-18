@extends('backend.admin_backend.admin_dashboard')
@section('admin')

<div class="container-fluid">

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">MANAGE  SUBJECTS COMBINATION</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Manage</a></li>
                    <li class="breadcrumb-item active"> Subject Combination</li>
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
        
                                        <h4 class="card-title">View Subject Combination</h4>

        
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Class</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                                
                                            
                                            </tr>
                                            </thead>
        
        
                                            <tbody>
                                                @foreach($results as $key => $result)

                                                <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ strtoupper($result->class_name) }}</td>
                                                <td>{{ ucwords(strtolower($result->subject_name)) }}</td>
                                                <td style="text-align:center;">
                                                    @if($result->status == 1 )
                                                    <span class="badge bg-success" style="padding:10px 15px; font-size:15px">Active</span>
                                                    @else
                                                    <span class="badge bg-danger" style="padding:10px 8px; font-size:15px">In-active</span>
                                                    @endif
                                                </td>
                                               

                                            
                                                <td style="text-align:center;" > 

                                                <a href="{{ route('delete.subject.combination',$result->id)}}" id="delete">
                                                <button type="submit"  class="btn btn-danger waves-effect waves-light">Delete</button>
                                                </a>


                                                   {{-- @if($result->status == 1) --}}
                                               <!-- <a href="{{ route('deactivate.subject.combination',$result->id)}}" style="color: #444; margin-left: 30px">
                                               <i class="fas fa-check"></i>
                                                

                                                    {{-- @else --}}
                                                <a href="{{ route('deactivate.subject.combination',$result->id)}}" style="color: #444; margin-left: 30px">
                                               <i class="fas fa-times"></i>
                                                </a> 
                                                {{-- @endif --}}
                                                  -->

                                                
                                             
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
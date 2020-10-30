@extends('layouts.header')

@section('content')
@if(session()->has('status'))
<div class="alert alert-success alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{session()->get('status')}}
</div>
@endif
{{-- {{auth()->user()->id}} --}}
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        <a ><button class="btn btn-primary" data-target="#new_request" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request </button></a>
                        {{-- <a href='{{ url('/sb-new-request-non-sap') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request Non-SAP</button></a> --}}
                        @include('new')
                        <table  class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th > Reference No. </th>
                                    <th > Name </th>
                                    <th > Company</th>
                                    <th > Department</th>
                                    <th > Approvers  </th>
                                    <th > Date Request  </th>
                                    <th > Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sb_requests as $request)
                               <tr>
                                   
                                <td >{{$request->company_info->company_abbreviation}}-{{date('Ym',strtotime($request->created_at))}}-{{str_pad($request->ref_id, 4, '0', STR_PAD_LEFT)}}</td>
                                <td >{{$request->user_info->name}}</td>
                               <td > {{$request->company_info->company_abbreviation}}</td>
                                <td > {{$request->department_info->name}}</td>
                                <td >
                                    @foreach($request->approvers_info as $approver)
                                    {{$approver->user_info->name}}  
                                    
                                    @if($approver->status == "Pending") <span class="label">{{$approver->status}} </span> 
                                    @if($request->level == $approver->role_number)   <button class='btn btn-primary btn-xs' onclick='email_manual({{$approver->id}})'>Send Follow Up</button> @endif <span style='color:green;display:none;' id='info{{$approver->id}}'>Email successfully sent!</span>
                                   
                                    @endif 
                                   
                                    @if($approver->status == "Approved") <span class="label label-primary">{{$approver->status}}</span> @endif 
                                    @if($approver->status == "Declined") <span class="label label-danger">{{$approver->status}}</span> @endif 
                                    
                                    <br> <br>
                                    @endforeach
                                
                                </td>
                                <td>
                                    {{date('M d, Y',strtotime($request->created_at))}}
                                </td>
                                <td > 
                                    <button class="btn btn-sm btn-primary" data-target="#view{{$request->id}}" data-toggle="modal" >View</button> 
                                    <button class="btn btn-sm btn-danger" data-target="#cancel_remarks{{$request->id}}" data-toggle="modal">Cancel</button> </td>
                               </tr>
                               @include('view_request_main')
                               @include('cancel_remarks')
                               @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer">

</div>
<script type='text/javascript'>
      function email_manual(approver_id)
            {
                document.getElementById("myDiv").style.display="block";
                $.ajax(
                {  //create an ajax request to load_page.php
                    type: "GET",
                    url: "{{ url('/manual-email') }}",            
                    data:
                    {
                        "approver_id" : approver_id,
                    }     ,
                    dataType: "json",   //expect html to be returned
                    success: function(data)
                    {
                        console.log(data);

                        document.getElementById("info"+data.id).style.display="block";
                        document.getElementById("myDiv").style.display="none";
                    }
                    ,
                    error: function(e)
                    {
                        console.log(e);
                        document.getElementById("myDiv").style.display="none";
                    }
                }
                );
            }
                  
</script>
@endsection

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
     
        <div class="col-lg-6">
            <h3><b><i> Supplement Budget</i></b></h3>
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a ><button class="btn btn-primary" data-target="#new_request" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request </button></a> --}}
                        {{-- <a href='{{ url('/sb-new-request-non-sap') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request Non-SAP</button></a> --}}
                        {{-- @include('new') --}}
                        <table  class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th > Reference No. </th>
                                    <th > Requestor </th>
                                    <th > Company</th>
                                    <th > Department</th>
                                    <th > Current Status </th>
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
                                <td>
                                    {{$request->last_status}}
                                </td>
                                <td > 
                                    <button class="btn btn-sm btn-primary" data-target="#view{{$request->id}}" data-toggle="modal" >View</button> 
                                    {{-- <button class="btn btn-sm btn-danger" data-target="#cancel_remarks{{$request->id}}" data-toggle="modal">Cancel</button> --}}
                                 </td>
                               </tr>
                               @include('view_request_main')
                               {{-- @include('cancel_remarks') --}}
                               @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox ">
                <h3><b><i> Re allocation</i></b></h3>
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a ><button class="btn btn-primary" data-target="#new_request" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request </button></a> --}}
                        {{-- <a href='{{ url('/sb-new-request-non-sap') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request Non-SAP</button></a> --}}
                        {{-- @include('new') --}}
                        <table  class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th > Reference No. </th>
                                    <th > Requestor </th>
                                    <th > Company</th>
                                    <th > Department</th>
                                    <th > Date Request  </th>
                                    <th > Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($re_allocations as $request)
                               <tr>
                                   
                                <td >{{$request->company_info->company_abbreviation}}-{{date('Ym',strtotime($request->created_at))}}-{{str_pad($request->ref_id, 4, '0', STR_PAD_LEFT)}}</td>
                                <td >{{$request->user_info->name}}</td>
                               <td > {{$request->company_info->company_abbreviation}}</td>
                                <td > {{$request->department_info->name}}</td>
                                <td>
                                    {{date('M d, Y',strtotime($request->created_at))}}
                                </td>
                                <td > 
                                    <button class="btn btn-sm btn-primary" data-target="#view_realloc{{$request->id}}" data-toggle="modal" >View</button> 
                                    {{-- <button class="btn btn-sm btn-danger" data-target="#cancel_remarks{{$request->id}}" data-toggle="modal">Cancel</button> --}}
                                 </td>
                               </tr>
                               @include('view_realloc_a')
                               {{-- @include('view_request_main') --}}
                               {{-- @include('cancel_remarks') --}}
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
  
                  
</script>
@endsection

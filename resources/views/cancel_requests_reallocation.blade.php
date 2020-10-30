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
                        {{-- <a href='{{ url('/new-request-realloc') }}'><button class="btn btn-primary" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request </button></a> --}}
                        {{-- <a href='{{ url('/sb-new-request-non-sap') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request Non-SAP</button></a> --}}
                        {{-- @include('new') --}}
                        <table  class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th > Reference No. </th>
                                    <th > Name </th>
                                    <th > Company</th>
                                    <th > Department</th>
                                    <th > Cancelled By  </th>
                                    <th > Remarks  </th>
                                    <th > Date Cancelled  </th>
                                    <th > Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sb_requests as $request)
                                {{-- {{}} --}}
                               <tr>
                                   
                                <td >{{$request->company_info->company_abbreviation}}-{{date('Ym',strtotime($request->created_at))}}-{{str_pad($request->ref_id, 4, '0', STR_PAD_LEFT)}}</td>
                                <td >{{$request->user_info->name}}</td>
                               <td > {{$request->company_info->name}}</td>
                                <td > {{$request->department_info->name}}</td>
                                <td >
                                    {{$request->cancel_info->name}}
                                </td>
                                <td >
                                    {{$request->remarks}}
                                </td>
                                <td>
                                    {{date('M. d. Y',strtotime($request->updated_at))}}
                                </td>
                                <td > 
                                    <button class="btn btn-sm btn-primary" data-target="#view{{$request->id}}" data-toggle="modal">View</button> 
                                    {{-- <button class="btn btn-sm btn-danger" data-target="#cancel_remarks{{$request->id}}" data-toggle="modal">Cancel</button> </td> --}}
                                    <button class="btn btn-sm btn-warning" data-target="#view_r{{$request->id}}" data-toggle="modal">Refile</button> 
                               </tr>
                               @include('view_realloc')
                               @include('view_r')
                               {{-- @include('cancel_remarks_realloc') --}}
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

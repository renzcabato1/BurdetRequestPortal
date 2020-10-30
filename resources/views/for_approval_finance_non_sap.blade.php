@extends('layouts.header')

@section('content')
@if(session()->has('status'))
<div class="alert alert-success alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{session()->get('status')}}
</div>
@endif
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                            
                        <table  class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th > Reference Number </th>
                                    <th > Approver </th>
                                    <th > Requestor</th>
                                    <th > Company</th>
                                    <th > Department  </th>
                                    <th > Date Request  </th>
                                    <th > Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sb_requests as $request)
                                @if($request->role_number == $request->sb_request->level)
                                    <tr>
                                        <td > {{$request->sb_request->company_info->company_abbreviation}}-{{date('Ym',strtotime($request->sb_request->created_at))}}-{{str_pad($request->sb_request->ref_id, 4, '0', STR_PAD_LEFT)}}</td>
                                        <td > {{$request->user_info->name}} </td>
                                        <td >  {{$request->sb_request->user_info->name}} </td>
                                        <td > {{$request->sb_request->company_info->company_abbreviation}}</td>
                                        <td >  {{$request->sb_request->department_info->name}}</td>
                                        <td>
                                            
                                            {{date('M. d, Y',strtotime($request->sb_request->created_at))}}
                                        </td>
                                        <td > 
                                            <button data-target="#view{{$request->id}}" data-toggle="modal" class="btn btn-sm btn-primary" >View</button> 
                                           <button data-toggle="modal"  data-target="#approve{{$request->id}}" data-toggle="modal" class="btn btn-sm btn-success" class="btn btn-sm btn-success" >Approve</button>
                                            <button data-target="#declined{{$request->id}}" data-toggle="modal" class="btn btn-sm btn-danger" >Decline</button> 
                                        </td>
                                    </tr>
                                    @include('view_request')
                                    @include('approve_request')
                                    @include('declined_request')
                                @endif
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

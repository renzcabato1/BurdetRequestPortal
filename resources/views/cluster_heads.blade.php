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
        <div class="col-lg-6">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_data')     --}}
                        {{-- <button class="btn btn-primary" data-target="#new_data" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Data</button></a> --}}
                        <table id='companies' class="table table-striped table-bordered table-hover " >
                            <thead>
                                <tr>
                                    <th > Company </th>
                                    <th > Cluster Head</th>
                                    <th > Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                <tr>
                                    <td>{{$company->company_abbreviation}}</td>
                                    @if($company->id == 17)
                                        <td>
                                           @foreach($company->approver_plant as $approver)
                                           {{$approver->plant_name}} : @if($approver->cluster_head != null){{$approver->cluster_head_info->name}} @endif
                                            <br>
                                            @endforeach
                                        </td>
                                    @else
                                        <td>@if($company->cluster_head){{$company->cluster_head->user_info->name}}@endif</td>
                                    @endif
                                    <td> <a onclick='' data-target="#edit_finance{{$company->id}}" data-toggle="modal" type="button"><i title='edit' class="fa fa-edit"></i></a></td>
                                </tr>
                                @include('edit_cluster_head') 
                                @endforeach
                               {{-- @endforeach --}}
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

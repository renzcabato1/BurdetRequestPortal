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
        <div class="col-lg-10">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('new_data')    
                        <button class="btn btn-primary" data-target="#new_data" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Data</button></a>
                        <table id='companies' class="table table-striped table-bordered table-hover " >
                            <thead>
                                <tr>
                                    <th > Company </th>
                                    <th > Budget Code</th>
                                    <th > Budget Description</th>
                                    <th > Material Description</th>
                                    <th > Cost Center</th>
                                    <th > Unit of Measure</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($budget_codes as $budgetcode)
                               <tr>
                                    <td> {{$budgetcode->company_info->name}}</td>
                                   <td> {{$budgetcode->budget_code}}</td>
                                   <td> {{$budgetcode->budget_description}}</td>
                                   <td> {{$budgetcode->material_description}}</td>
                                   <td> {{$budgetcode->cost_center_description}}</td>
                                   <td>{{$budgetcode->unit_of_measure}} </td>
                                  
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
<div class="footer">
    
</div>
<script type='text/javascript'>
    
    
</script>
@endsection

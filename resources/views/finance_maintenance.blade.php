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
        <div class="col-lg-8">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                        
                        <table id='companies' class="table table-striped table-bordered table-hover " >
                            <thead>
                                <tr>
                                    <th > Company </th>
                                    <th > Company Code</th>
                                    <th > Controlling Area</th>
                                    <th > Order Types</th>
                                    <th > 1st char.</th>
                                    <th > Plants : Business Area</th>
                                    <th > Finance Head</th>
                                    <th > Action  </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                    <tr>
                                        <td > {{$company->company_abbreviation}} </td>
                                        {{-- <td > @if(count($company->finance_coors))
                                            @foreach($company->finance_coors as $finance_coor) 
                                                {{$finance_coor->user_info->name}} <br>
                                            @endforeach
                                        
                                            @endif
                                        </td> --}}
                                        <td> @if($company->company_info != null) {{$company->company_info->name}} @endif</td>
                                        <td> @if($company->controlling_area != null) {{$company->controlling_area->controlling_area}} @endif</td>
                                        <td>
                                            @if(count($company->order_types))
                                                @foreach($company->order_types as $order_type) 
                                                    {{$order_type->order_type}} <br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if(count($company->assign_letters))
                                                @foreach($company->assign_letters as $assign_letter) 
                                                    {{$assign_letter->assign_letter}} <br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if(count($company->plant_info))
                                                @foreach($company->plant_info as $plant) 
                                                    {{$plant->plant}} :  @if($plant->business_area != null){{$plant->business_area}}@else Not Applicable @endif<br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td > @if($company->finance_heads){{$company->finance_heads->user_info->name}}@endif </td>
                                        <td > 
                                            <a onclick='' data-target="#edit_finance{{$company->id}}" data-toggle="modal" type="button"><i title='edit' class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    @php
                                        $finance = $company->finance_coors;
                                        $order_types = $company->order_types;
                                        $assign_letters = $company->assign_letters;
                                        $ids = $finance->pluck('user_id');
                                        $order_types_ids = $order_types->pluck('order_type');
                                        $assign_letters_ids = $assign_letters->pluck('assign_letter');
                                    @endphp
                                     @include('edit_finances') 
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        Endorsement<br>
                        @include('new_endorsement')    
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                        <button class="btn btn-primary" data-target="#new_endorsement" data-toggle="modal" type="button"> + &nbsp;New </button>
                           
                        <table   class="table table-striped table-bordered table-hover " >
                            <thead>
                                <tr>
                                    <th > Employee  </th>
                                    <th > Position  </th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($endorsements as $endorsement)
                                <tr>
                                    <td>{{strtoupper($endorsement->user_info->name)}}
                                    </td>
                                    <td>{{$endorsement->position}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                        
                        <table id='last_approver'  class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th > Last Approver </th>
                                    <th > Sign  </th>
                                    <th > Amount  </th>
                                    <th > Action  </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($last_approvers as $approver)
                                    <tr>
                                        <td > {{$approver->user_info->name}} </td>
                                        <td > {{$approver->sign}}  </td>
                                        <td > {{number_format($approver->amount,2)}}  </td>
                                        <td > 
                                            <button data-target="#edit_approver{{$approver->id}}" data-toggle="modal" type="button" class="btn btn-sm btn-primary" >Edit</button> 
                                        </td>
                                    </tr>
                                    @include('edit_approver') 
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('new_unit_of_measure')    
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                        <button class="btn btn-primary" data-target="#new_unit_of_measure" data-toggle="modal" type="button"> + &nbsp;New </button>
                           
                        <table id='unit_of_measure'  class="table table-striped table-bordered table-hover " >
                            <thead>
                                <tr>
                                    <th > Unit of Measure</th>
                                    <th > Action  </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unit_of_measures as $unit_of_measure)
                                    <tr>
                                        <td > {{$unit_of_measure->name}} </td>
                                        <td > 
                                            <button data-target="#unit_of_measure{{$unit_of_measure->id}}" data-toggle="modal" class="btn btn-sm btn-primary" >Edit</button> 
                                        </td>
                                    </tr>
                                    @include('edit_unit_of_measure') 
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        General Manager/COO <br>
                        @include('new_coo')    
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                        <button class="btn btn-primary" data-target="#new_coo" data-toggle="modal" type="button"> + &nbsp;New </button>
                           
                        <table id='COO'  class="table table-striped table-bordered table-hover " >
                            <thead>
                                <tr>
                                    <th >Company</th>
                                    <th > GM/COO  </th>
                                    {{-- <th > Action  </th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($general_managers as $manager)
                                    <tr>
                                        <td>{{$manager->company_info->company_abbreviation}}
                                        </td>
                                        <td>{{$manager->user_info->name}}
                                        </td>
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

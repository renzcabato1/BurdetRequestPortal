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
                <div class="ibox-title">
                    <h5>Approved SB   </h5>
                
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                            
                        <table  class="table table-striped table-bordered table-hover " id='upload_budget' >
                            <thead>
                                <tr  style='display:none;'>
                                    <th >  </th>
                                    <th >COSP_VERSN</th>
                                    <th > COSP-GJAHR</th>
                                    <th > BKPF-MONAT</th>
                                    <th > COSP-OBJNR</th>
                                    <th >COSP-KTEXT if COAS-AUFNR= COSP-OBJNR </th>
                                    <th >COSP-MEG001-16</th>
                                    <th >COSP-MEINH</th>
                                    <th ></th>
                                    <th >COSP-WKG001-16 </th>
                                </tr>
                               
                            </thead>
                            <tbody>
                                <tr>
                                    <td > Company</td>
                                    <td > Requestor </td>
                                    <td > Fiscal Year </td>
                                    <td > Period </td>
                                    <td >  IO Description</th>
                                    <td > Quantity </td>
                                    <td > Unit </td>
                                    <td > Unit Cost </td>
                                    <td > Total SB (VAT EX) </td>
                                    <td > Total SB (VAT INC) </td>
                                </tr>
                                
                                @foreach($sup_details as $sup_detail)
                                @php
                                    $date = explode("-", $sup_detail->date_needed);
                                @endphp

                                <tr>
                                    <td >{{$sup_detail->sb_request->company_info->company_abbreviation}} </td>
                                    <td > {{$sup_detail->sb_request->user_info->name}}  </td>
                                    <td > {{$date[0]}}   </td>
                                    <td > {{$date[1]}}   </td>
                                    <td > {{$sup_detail->io_description}}   </td>
                                    <td > {{$sup_detail->qty}}   </td>
                                    <td > {{$sup_detail->unit_of_measure}}   </td>
                                    <td >  @if($sup_detail->qty != null ) {{$sup_detail->unit_price}}  @endif</td>
                                    <td > @if($sup_detail->qty != null ){{(($sup_detail->qty * $sup_detail->unit_price))}} @else {{$sup_detail->unit_price}}   @endif </td>
                                    <td > @if($sup_detail->no_vat == "Yes") 
                                                @if($sup_detail->qty != null ){{(($sup_detail->qty * $sup_detail->unit_price))}} @else {{$sup_detail->unit_price}} @endif
                                          @else 
                                                @if($sup_detail->qty != null ){{number_format((($sup_detail->qty * $sup_detail->unit_price))*1.12,2)}} @else {{number_format(($sup_detail->unit_price*1.12),2)}} @endif
                                          @endif
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

@endsection

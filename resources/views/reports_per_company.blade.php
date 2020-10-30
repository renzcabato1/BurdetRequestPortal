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
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                            
                        <table  class="table table-striped table-bordered table-hover company-report" >
                            <thead>
                                <tr>
                                    <th > Company </th>
                                    <th > Total submit SB </th>
                                    <th > Total Declined SB</th>
                                    <th > Total Approved SB</th>
                                    <th > Total Amount Approved</th>
                                
                                </tr>
                           
                            </thead>
                            <tbody>
                               
                                @foreach($companies as $company)
                                <tr>
                                    <td>{{$company->name}}</td>
                                    <td>{{count($company->total)}}</td>
                                    <td>{{count($company->total_declined)}}</td>
                                    <td>{{count($company->total_approved)}}</td>
                                    <td>
                                        @php
                                            $total_amount = 0;
                                        @endphp
                                        @foreach($company->total_approved as $total_approved)
                                            @foreach($total_approved->details as $detail)
                                                @if($detail->qty != null)
                                                    @php
                                                        $total_amount = $total_amount + ($detail->qty * $detail->unit_price);
                                                    @endphp
                                                @else
                                                    @php
                                                        $total_amount = $total_amount + ($detail->unit_price);
                                                    @endphp
                                                @endif
                                            @endforeach
                                        @endforeach
                                        {{number_format($total_amount,2)}}
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

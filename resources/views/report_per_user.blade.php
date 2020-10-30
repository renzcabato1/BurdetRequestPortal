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
                    <form  method='GET'>
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="font-normal">Company </label>
                                <select data-placeholder="Choose Company" name='company' class="chosen-select "   id='company_id' required="true" tabindex="1" required>
                                    <option value=""></option>
                                    @foreach($companies as $company)
                                    <option  value="{{$company->id}}"  {{($company->id == $comp) ? "selected":"" }}>{{$company->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="font-normal">Date</label>
                                <input type='month' name='month' class='form-control' value='{{$date_select}}' required>
                            </div>
                            <div class="col-lg-4">
                                <label class="font-normal">&nbsp; </label>
                                <button class="btn btn-primary " type="submit" id='submit'><i class="fa fa-check"></i>&nbsp;Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                        
                        <table  class="table table-striped table-bordered table-hover company-report" >
                            <thead>
                                <tr>
                                    <th > User </th>
                                    <th > Total submit SB </th>
                                    <th > Total Declined SB</th>
                                    <th > Total Approved SB</th>
                                    <th > Total Amount Approved</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($comp)
                                @foreach($users as $user)
                                <tr>
                                    <td >{{$user->user_info->name}} </td>
                                    <td >0</td>
                                    <td > 0</td>
                                    <td > 0</td>
                                    <td > 0</td>
                                </tr>
                                @endforeach
                                @endif
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

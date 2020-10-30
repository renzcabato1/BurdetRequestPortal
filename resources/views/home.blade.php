@extends('layouts.header')

@section('content')
<div class="wrapper wrapper-content">
    <h3><b><i>Supplemental Budget</i></b></h3>
    <div class="row">
        <hr>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>Pending Request </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/sb-request') }}">{{$sb_requests}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        @php
            $for_approvals_count = 0;
        @endphp
        @foreach($for_approvals as $request)
            @if(($request->sb_request->original_approver[0]->approver_id == $request->sb_request->original_approver[1]->approver_id) && ($request->sb_request->original_approver[1]->approver_id == $request->sb_request->original_approver[2]->approver_id))
                @if(count($request->addtional_approver) == 0)
                    @if(($request->role_number == $request->sb_request->level))
                        @php
                            $for_approvals_count  = $for_approvals_count +1;
                        @endphp
                    @endif
                @else
                    @if(($request->role_number == 0) && ($request->status == "Pending"))
                        @php
                            $for_approvals_count  = $for_approvals_count +1;
                        @endphp
                    @endif
                @endif
        @elseif($request->sb_request->original_approver[1]->approver_id == $request->sb_request->original_approver[2]->approver_id)
            @if(count($request->addtional_approver) == 0)
                @if(($request->role_number == $request->sb_request->level))
                    @php
                        $for_approvals_count  = $for_approvals_count +1;
                    @endphp
                @endif 
            @else
                @if($request->sb_request->level == 2)
                    @if(($request->role_number == 0) && ($request->status == "Pending"))
                        @php
                            $for_approvals_count  = $for_approvals_count +1;
                        @endphp
                    @endif
                @else
                    @if(($request->role_number == $request->sb_request->level))
                        @php
                            $for_approvals_count  = $for_approvals_count +1;
                        @endphp
                    @endif 
                @endif
            @endif
        @else
            @if(count($request->addtional_approver) == 0)
                @if(($request->role_number == $request->sb_request->level))
                    @php
                        $for_approvals_count  = $for_approvals_count +1;
                    @endphp
                @endif 
            @else
                @if($request->sb_request->level == 3)
                    @if(($request->role_number == 0) && ($request->status == "Pending"))
                        @php
                            $for_approvals_count  = $for_approvals_count +1;
                        @endphp
                    @endif
                @elseif($request->sb_request->level<=2)
                    @if(($request->role_number == $request->sb_request->level))
                        @php
                            $for_approvals_count  = $for_approvals_count +1;
                        @endphp
                    @endif    
                @endif
            @endif
        @endif
        @endforeach
       
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>For Approval</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/sb-for-approval') }}">{{$for_approvals_count}}</a></h1>
                    
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Approved Request </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/sb-approved') }}">{{$approve_requests}}</a></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Cancelled / Declined Request </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/sb-cancelled') }}">{{$cancelled_requests}}</a></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Review Again Request </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/review-again') }}">{{$review_again}}</a></h1>
                </div>
            </div>
        </div>
      
    </div>
    <h3><b><i>Reallocation </i></b></h3>
    <div class="row">
        <hr>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>Pending Request </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/re-request') }}">{{$reallocation}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        @php
            $for_approvals_count = 0;
        @endphp
        @foreach($re_allocation_for_approval as $request)
        @if(($request->role_number == $request->sb_request->level) || (($request->role_number == 0)&&($request->status == "Pending")))
            @php
                $for_approvals_count  = $for_approvals_count +1;
            @endphp
        @endif
        @endforeach
       
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-primary float-right">As of Today</span> --}}
                    <h5>For Approval</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/re-for-approval') }}">{{$for_approvals_count}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-navy">0</i></div>
                    <small>Approved today</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-primary float-right">As of Today</span> --}}
                    <h5>Approved Request </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/re-approved') }}">{{$reallocation_approved}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-navy">0</i></div>
                    <small>Approved today</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-primary float-right">As of Today</span> --}}
                    <h5>Cancelled / Declined Request </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/re-cancelled') }}">{{$cancelled_request_reallocation}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-navy">0</i></div>
                    <small>Approved today</small> --}}
                </div>
            </div>
        </div>
      
    </div>
    <h3><b><i>Action History </i></b></h3>
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>Approved (SB) </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/approved-request-approver') }}">{{$approved_history}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>Declined (SB) </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/declined-request-approver') }}">{{$declined_history}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>Approved (Reallocation) </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/approved-request-approver') }}">{{$re_allocations_count}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>Declined (Reallocation) </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/declined-request-approver') }}">{{$re_allocations_declined}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>Review Again </h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/review-again-history') }}">{{$review_again_history}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
    </div>
    @php
        $roles = auth()->user()->role_info();
        $finance_sap = 0;
        $finance_non_sap = 0;
    @endphp
    @if(!(auth()->user()->role_info())->isEmpty())
        @foreach($roles as $role)
            @if($role->company_info->controlling_area != null)
                @php
                    $finance_sap = 1;
                @endphp
            @endif
            @if($role->company_info->controlling_area == null)
                @php
                    $finance_non_sap = 1;
                @endphp
            @endif
        @endforeach
    @endif
    {{-- {{$finance_non_sap}} --}}
    @if(($finance_sap == 1) || ((count(auth()->user()->plant_info()) != 0)))
    <h3><b><i>Finance SAP Head </i></b></h3>
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>For Finance Approval (SB)</h5>
                </div>
                @php
                    $finance_for_approval_count = 0;
                @endphp
                @foreach($finance_for_approval as $finance)
                    @if($finance->role_number == $finance->sb_request->level)
                        @php
                            $finance_for_approval_count = $finance_for_approval_count + 1;
                        @endphp
                    @endif
                @endforeach
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/sb-for-approval-finance') }}">{{$finance_for_approval_count}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-primary float-right">As of Today</span> --}}
                    <h5>Pending for Upload IO Master (SB)</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/for-upload') }}">{{$create_ios}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-navy">0</i></div>
                    <small>Approved today</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-primary float-right">As of Today</span> --}}
                    <h5>Pending for Upload Budget Amount (SB)</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/for-upload') }}">{{$sup_details}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-navy">0</i></div>
                    <small>Approved today</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-primary float-right">As of Today</span> --}}
                    <h5>Upload Budget Amount (Re allocation)</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/re-for-upload') }}">{{$for_upload_reallocation}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-navy">0</i></div>
                    <small>Approved today</small> --}}
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($finance_non_sap === 1) 
    <h3><b><i>Finance Non SAP Head</i></b></h3>
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-success float-right">Monthly</span> --}}
                    <h5>For Finance Approval (SB)</h5>
                </div>
                @php
                    $finance_for_approval_count = 0;
                @endphp
                @foreach($finance_for_approval_non_sap as $finance)
                    @if($finance->role_number == $finance->sb_request->level)
                        @php
                            $finance_for_approval_count = $finance_for_approval_count + 1;
                        @endphp
                    @endif
                @endforeach
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/sb-for-approval-finance-non-sap') }}">{{$finance_for_approval_count}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-info"> 0 </div>
                    <small>New Request as of {{date('M. Y')}}</small> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    {{-- <span class="label label-primary float-right">As of Today</span> --}}
                    <h5>Approved Request NON SAP</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><a href="{{ url('/for-upload-non-sap') }}">{{$non_sap_approved_request}}</a></h1>
                    {{-- <div class="stat-percent font-bold text-navy">0</i></div>
                    <small>Approved today</small> --}}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="footer">
</div>
</div>
@endsection

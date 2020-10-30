<?php

namespace App\Http\Controllers;
use App\SbRequest;
use App\RequestApprover;
use App\Detail;
use App\ReAllocation;
use App\ReallocationApprover;
use App\ReallocationDetail;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     
    public function index()
    {

        $sb_requests = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')
        ->whereHas('details')
        ->whereHas('approvers_info')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Pending')
        ->orderBy('created_at','asc')
        ->count();
        $review_again = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Review Again')
        ->orderBy('created_at','asc')
        ->count();
        $review_again_history = SbRequest:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Review Again');
           
        })->with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments')
        ->where('last_status','=','Review Again')
        ->orderBy('created_at','asc')
        ->count();
        $approve_requests = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')->where('last_status','Approved')
        ->where('request_by',auth()->user()->id)
        ->orderBy('created_at','asc')->count();

        $cancelled_requests = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','cancel_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Cancelled')->orderBy('created_at','asc')->count();
        $header ='Home';
        $for_approvals = RequestApprover::with(
            'sb_request.user_info',
            'user_info','sb_request.company_info',
            'sb_request.department_info',
            'sb_request.details',
            'sb_request.attachments',
            'sb_request.approvers_info.user_info',
            'sb_request.original_approver',
            'sb_request.approvers_info.review_again_info.user_info',
            'addtional_approver',
            'review_again_info.user_info'
            )
            ->where('role_number','!=',5)
            // ->orWhere('role_number','=',0)
            ->whereHas('sb_request',function ($query) {
                $query->where('last_status','=',"Pending");  
            })
        ->where('approver_id',auth()->user()->id)
        ->orderBy('created_at','asc')
        ->get();

        $finance_for_approval = RequestApprover::with(
            'sb_request.user_info',
            'user_info','sb_request.company_info',
            'sb_request.department_info',
            'sb_request.details',
            'sb_request.attachments',
            'sb_request.approvers_info.user_info'
            
            )
            ->where('role_number','=',5)
            ->whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Pending")
            ->where('type','=',null);    
        })
        ->where('approver_id',auth()->user()->id)
        ->orderBy('created_at','asc')
        ->get();

        $finance_for_approval_non_sap = RequestApprover::with(
            'sb_request.user_info',
            'user_info','sb_request.company_info',
            'sb_request.department_info',
            'sb_request.details',
            'sb_request.attachments',
            'sb_request.approvers_info.user_info'
            
            )
            ->where('role_number','=',5)
            ->whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Pending")
            ->where('type','=','Non SAP');    
        })
        ->where('approver_id',auth()->user()->id)
        ->orderBy('created_at','asc')
        ->get();

        $for_upload_reallocation = ReallocationDetail::whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Approved");
            // ->where('type','!=','Non SAP');    
        })
        ->whereHas('approvers',function ($query) {
            $query->where('approver_id','=',auth()->user()->id) 
            ->where('role_number','=',4);  
        })
        ->where('date_uploaded_budget',null)->count();

        $sup_details = Detail::whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Approved")
            ->where('type','=',null);    
        })
        ->whereHas('approvers',function ($query) {
            $query->where('approver_id','=',auth()->user()->id) 
            ->where('role_number','=',5);  
        })
        ->where('date_uploaded_budget',"=",null)->count();

      
        $create_ios = Detail::whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Approved")
            ->where('type','=',null);   
        })
        ->whereHas('approvers',function ($query) {
            $query->where('approver_id','=',auth()->user()->id) 
            ->where('role_number','=',5);  
        })
        ->where('create_io_date',"=",null)->where('budgeted',"=","Not Budgeted")->count();

        $non_sap_approved_request = Detail::whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Approved")
            ->where('type','=','Non SAP');  
        })
        ->whereHas('approvers',function ($query) {
            $query->where('approver_id','=',auth()->user()->id) 
            ->where('role_number','=',5);  
        })
        ->count();

        $reallocation = ReAllocation::with('user_info','company_info','department_info','details','approvers_info.employee_info','approvers_info.user_info','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Pending')
        ->orderBy('created_at','asc')
        ->count();
        $re_allocation_for_approval = ReallocationApprover::with(
            'sb_request.user_info',
            'user_info','sb_request.company_info',
            'sb_request.department_info',
            'sb_request.details',
            'sb_request.attachments',
            'sb_request.approvers_info.user_info'
            )
            ->whereHas('sb_request',function ($query) {
                $query->where('last_status','=',"Pending");  
            })
        ->where('approver_id',auth()->user()->id)
        ->orderBy('created_at','asc')
        ->get();

        $reallocation_approved = Reallocation::with('user_info','company_info','department_info','approvers_info.user_info','cancel_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Approved')->orderBy('created_at','asc')->count();
        $cancelled_request_reallocation = Reallocation::with('user_info','company_info','department_info','approvers_info.user_info','cancel_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Cancelled')->orderBy('created_at','asc')->count();


        $approved_history = SbRequest:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Approved');
           
        })->with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments')
      
        ->orderBy('created_at','asc')
        ->count();
        $declined_history = SbRequest:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Declined');
           
        })->with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments')
      
        ->orderBy('created_at','asc')
        ->count();


        $re_allocations_count = ReAllocation:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Approved');
        })->with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')
      
        ->orderBy('created_at','asc')
        ->count();

        $re_allocations_declined = ReAllocation:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Declined');
        })->with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')
      
        ->orderBy('created_at','asc')
        ->count();
        
        return view('home',array(

            'subheader' => 'Dashboards',
            'header' => $header,
            'sb_requests' => $sb_requests,
            'approve_requests' => $approve_requests,
            'cancelled_requests' => $cancelled_requests,
            'finance_for_approval' => $finance_for_approval,
            'finance_for_approval_non_sap' => $finance_for_approval_non_sap,
            'sup_details' => $sup_details,
            'create_ios' => $create_ios,
            'for_approvals' => $for_approvals,
            'non_sap_approved_request' => $non_sap_approved_request,
            'reallocation' => $reallocation,
            're_allocation_for_approval' => $re_allocation_for_approval,
            'reallocation_approved' => $reallocation_approved,
            'cancelled_request_reallocation' => $cancelled_request_reallocation,
            'approved_history' => $approved_history,
            'declined_history' => $declined_history,
            're_allocations_count' => $re_allocations_count,
            're_allocations_declined' => $re_allocations_declined,
            'review_again' => $review_again,
            'review_again_history' => $review_again_history,
            'for_upload_reallocation' => $for_upload_reallocation,
        ));
    }
}

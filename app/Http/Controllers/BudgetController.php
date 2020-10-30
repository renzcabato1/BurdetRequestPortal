<?php

namespace App\Http\Controllers;
use App\Company;
use App\Department;
use App\Employee;
use App\AssignHead;
use App\UnitOfMeasure;
use App\SbRequest;
use App\Detail;
use App\Attachment;
use App\Reason;
use App\SapUser;
use App\CompanyCode;
use App\Deta;
use App\Plant;
use App\RequestApprover;
use App\ReAllocation;
use App\ControllingArea;
use App\Endorsement;
use App\ReallocationDetail;
use App\ReallocationApprover;
use App\ReallocationAttachment;
use App\Currency;
use App\User;
use App\Payroll;
use App\CostCenter;
use App\BudgetCode;
use App\RequestApproverRemark;
use App\Notifications\SaveRequest;
use App\Notifications\ForApprovalNotif;
use App\Notifications\NextApproverNotif;
use App\Notifications\ApproveRequest;
use App\Notifications\EndorseNotif;
use App\Notifications\DeclinedRequest;
use App\Notifications\RequestReallocation;
use App\Notifications\ReviewAgain;
use App\Notifications\ReplyReviewAgain;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function request()
    {
        $sb_requests = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments','approvers_info.review_again_info.user_info')
        ->whereHas('details')
        ->whereHas('approvers_info')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Pending')
        ->orderBy('created_at','asc')
        ->get();
        $companies = Company::whereHas('finance_heads')->orderBy('name','asc')->get();
       
        // dd($sb_requests[0]);
        return view('sb_request',array(

            'sb_requests' => $sb_requests,
            'companies' => $companies,
            'subheader' => 'Request',
            'header' => 'Supplemental Budget',
        ));
    }
    public function review_again_request()
    {
        $sb_requests = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Review Again')
        ->orderBy('created_at','asc')
        ->get();
        $companies = Company::whereHas('finance_heads')->orderBy('name','asc')->get();
       
        // dd($sb_requests[0]);
        return view('review_again_request',array(

            'sb_requests' => $sb_requests,
            'companies' => $companies,
            'subheader' => 'Review Again Request',
            'header' => 'Supplemental Budget',
        ));
    }
    public function forApproval()
    {
        $sb_requests = RequestApprover::with(
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
        $reasons = Reason::orderBy('reason','asc')->get();
        return view('sb_for_approval',array(

            'sb_requests' => $sb_requests,
            'reasons' => $reasons,
            'subheader' => 'For approval',
            'header' => 'Supplemental Budget',
        ));
    }
    public function for_approval_finance()
    {
        $sb_requests = RequestApprover::with(
            'sb_request.user_info',
            'user_info','sb_request.company_info',
            'sb_request.department_info',
            'sb_request.details',
            'sb_request.attachments',
            'sb_request.approvers_info.user_info'
            
            )
            ->whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Pending")
            ->where('type','=',null);  
        })
        ->where('role_number','=',5)
        ->where('approver_id',auth()->user()->id)
        ->orderBy('created_at','asc')
        ->get();
        // dd($sb_requests);
        $reasons = Reason::orderBy('reason','asc')->get();
        return view('for_approval_finance',array(
            'sb_requests' => $sb_requests,
            'reasons' => $reasons,
            'subheader' => 'For Finance Approval',
            'header' => 'Supplemental Budget',
        ));
    }
    public function finance_for_approval_non_sap ()
    {
        $sb_requests = RequestApprover::with(
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
        // dd($sb_requests);
        $reasons = Reason::orderBy('reason','asc')->get();
        return view('for_approval_finance_non_sap',array(

            'sb_requests' => $sb_requests,
            'reasons' => $reasons,
            'subheader' => 'For Finance Approval Non SAP',
            'header' => 'Supplemental Budget',
        ));
    }
    public function sb_request (Request $request)
    {
        // dd($request->all());
        $r = $request->req;
        $emp = [];
        $company = $request->company;
        $company_info = Company::with('controlling_area','general_info.user_info','plant_info','cluster_head.user_info')->where('id',$company)->orderBy('name','asc')->first();
        //  dd($company_info);
        
        if($company_info->controlling_area != null)
        {
      
        $currencies = Currency::get();
      
        $companies = Company::whereHas('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
        $cluster_heads = Plant::with('cluster_head_info')->where('company_id',$company)->groupBy('cluster_head')->get(['cluster_head']);
        $cluster_heads_id = ($cluster_heads->pluck('cluster_head'))->toArray();
        $departments = Department::orderBy('name','asc')->get();
        $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
        $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
        // dd($employee_supervisor->employee_head_id);
        $employees = Endorsement::with('employee_info')->get();
        // dd($employees[1]);
        return view('sb_request_new',array(
            'subheader' => 'Request',
            'header' => 'Supplemental Budget',
            'companies' => $companies,
            'departments' => $departments,
            'unit_of_measures' => $unit_of_measures,
            'employee_company' => $employee_company,
            'employee_supervisor' => $employee_supervisor,
            'employee_bu_head' => $employee_bu_head,
            'employee_cluster_head' => $employee_cluster_head,
            'currencies' => $currencies,
            'employees' => $employees,
            'company_info' => $company_info,
            'cluster_heads' => $cluster_heads,
            'cluster_heads_id' => $cluster_heads_id,
            'r' => $r,
        ));
        }
        else
        {
            $currencies = Currency::get();
            $companies = Company::whereDoesntHave('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
            $departments = Department::orderBy('name','asc')->get();
            $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
            $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
            $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
            $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
            $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
            $employees = Endorsement::with('employee_info')->get();
            $cost_centers = CostCenter::orderBy('dept_name')->get();
            return view('new_sb_non_sap',array(
                'subheader' => 'Request',
                'header' => 'Supplemental Budget',
                'companies' => $companies,
                'departments' => $departments,
                'unit_of_measures' => $unit_of_measures,
                'employee_company' => $employee_company,
                'employee_supervisor' => $employee_supervisor,
                'employee_bu_head' => $employee_bu_head,
                'employee_cluster_head' => $employee_cluster_head,
                'currencies' => $currencies,
                'employees' => $employees,
                'company_info' => $company_info,
                'cost_centers' => $cost_centers,
                'r' => $r,
            ));
        }
    }
    public function save_request(Request $request)
    {
        // dd($request->all());
        $date_today_from = date('Y-m-01');
        $date_today_to = date('Y-m-d');
        $company = Company::with('cluster_head')->where('id',$request->company)->first();
        $comp = $company->company_abbreviation;
        $ref_id = SbRequest::where('company_id',$request->company)->whereDate('created_at','>=',$date_today_from)->whereDate('created_at','<=',$date_today_to)->orderBy('id','desc')->first();
        // dd($request->all());
        if($ref_id == null)
        {
            $ref = 1;
        }
        else
        {
            $ref = $ref_id->ref_id + 1;
        }
        
        $sb_request = new SbRequest;
        $sb_request->company_id = $request->company;
        // dd($request->company);
        $sb_request->department_id = $request->department;
        // $sb_request->project_disbursement_date = $request->projected_disbursement_date;
        $sb_request->date_from_projected = $request->date_from_projected;
        $sb_request->date_to_projected = $request->date_to_projected;
        $sb_request->expected_delivery_date_from = $request->expected_delivery_date_from;
        $sb_request->expected_delivery_date_to = $request->expected_delivery_date_to;
        $sb_request->conversion_rate_used = $request->conversion_rate_used;
        $sb_request->last_status = "Pending";
        $sb_request->level = 1;
        $sb_request->ref_id = $ref;
        $sb_request->request_by = auth()->user()->id;
        $sb_request->save(); 
        $total = 0;
        foreach($request->budgted as $key => $budgeted)
        {
            $date_month = explode("-",$request->date_needed[$key]);
            $details = new Detail;
            $details->sb_request_id = $sb_request->id;
            if($request->qty != null)
            {
                if (array_key_exists($key,$request->qty))
                {
                    $details->qty = $request->qty[$key];
                }
            }
            if($request->no_vat != null)
            {
                if (array_key_exists($key,$request->no_vat))
                {
                    $details->no_vat = "Yes";
                }
            }
            if($request->unit_price[$key] == $request->vat_inclusive[$key])
            {
                $details->no_vat = "Yes";
            }
            if($request->type_of_request != null)
            {
                if (array_key_exists($key,$request->type_of_request))
                {
                    $details->type_of_request = $request->type_of_request[$key];
                }
            }
            if($request->unit_of_measure != null)
            {
                if (array_key_exists($key,$request->unit_of_measure))
                {
                    $details->unit_of_measure = $request->unit_of_measure[$key];
                }
            }
            if($request->cost_center != null)
            {
                if (array_key_exists($key,$request->cost_center))
                {
                    $details->cost_center = $request->cost_center[$key];
                }
            }
            if($request->plant != null)
            {
                if (array_key_exists($key,$request->plant))
                {
                    $value_plan = (explode("-",$request->plant[$key]));
                    $details->plant = $value_plan[0];
                }
            }
            if($request->material_code != null)
            {
                if (array_key_exists($key,$request->material_code))
                {
                    if($request->material_code[$key] != null)
                    {
                    $details->material_code = $request->material_code[$key];
                    $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company)->first();
                    $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
                     $sapConnection = [
                        'ashost' => $sap_server->sapServer->app_server,
                        'sysnr' => $sap_server->sapServer->system_number,
                        'client' => $sap_server->sapServer->client,
                        'user' => $sap_user->sap_id,
                        'passwd' => $sap_user->sap_password,
                    ];
                    $check_material_description = SapApiController::executeSapFunction($sapConnection,'ZFM_GET_BASEUNIT_INTEG',       
                    [
                        'I_MATNR' => $request->material_code[$key],
                    ]
                    , null);
                    $details->unit_of_measure = $check_material_description['O_BASEUNIT'];
                    $details->material_description = $check_material_description['O_MATDESC'];

                    }
                }
            }
            if($request->material_description != null)
            {
                if (array_key_exists($key,$request->material_description))
                {
                    $details->material_description = $request->material_description[$key];
                }
            }
            if($request->io_description != null)
            {
                if (array_key_exists($key,$request->io_description))
                {
                    $details->io_description = $request->io_description[$key];
                }
            }
            if($request->qty != null)
            {
                if (array_key_exists($key,$request->qty))
                {
                    if($request->qty[$key] != "")
                    {
                    $details->unit_price = $request->unit_price[$key]/$request->qty[$key];
                    }
                    else
                    {
                        $details->unit_price = $request->unit_price[$key];
                    }
                }
                else
                {
                    $details->unit_price = $request->unit_price[$key];
                }
            }
            else
            {
                $details->unit_price = $request->unit_price[$key];
            }
     
            $details->budgeted = $request->budgted[$key];
            if($request->budget_line != null)
            {
                if (array_key_exists($key,$request->budget_line))
                {
                    $details->budget_code = $request->budget_line[$key];
                    $gl_account = substr($request->budget_line[$key],1,4);
                    $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company)->first();
                    // dd($sap_server); 
                    $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
                    // return($sap_user);
                    $sapConnection = [
                        'ashost' => $sap_server->sapServer->app_server,
                        'sysnr' => $sap_server->sapServer->system_number,
                        'client' => $sap_server->sapServer->client,
                        'user' => $sap_user->sap_id,
                        'passwd' => $sap_user->sap_password,
                    ];
                    // return ($sapConnection);
                    $checkbudget = SapApiController::executeSapFunction($sapConnection,'ZFM_SUPBUD_INTEG',       
                    [
                        'I_AUFNR' => $request->budget_line[$key],
                        'I_PERIOD' => $date_month[1],
                        'I_YEAR' => $date_month[0],
                    ]
                    , null);
                    $data[] = $checkbudget;

                    $check_material_description = SapApiController::executeSapFunction($sapConnection,'ZFM_GET_BASEUNIT_INTEG',       
                    [
                        'I_MATNR' => $checkbudget['O_MATERIAL'],
                    ]
                    , null);
                    if($checkbudget['O_MATERIAL'] != "")
                    {

                        $details->material_code = intval($checkbudget['O_MATERIAL']);
                    }
                    else
                    {
                        $details->material_code = ($checkbudget['O_MATERIAL']);
                    }
                        // $details->gl_account = $check_material_description['O_GLACCOUNT'];
                        $details->material_description = $check_material_description['O_MATDESC'];
                        $details->unit_of_measure = $check_material_description['O_BASEUNIT'];
                        $details->io_description = $checkbudget['O_IODESC'];
                        $details->version = $checkbudget['O_VERSN'];
                        $details->order_type = $checkbudget['O_ORDRTYPE'];
                        $details->cost_center = $checkbudget['O_RESTCENTER'];
                        $details->company_code = $checkbudget['O_COMPCODE'];
                        $details->company_code = $checkbudget['O_COMPCODE'];
                        $details->month_sap = $checkbudget['O_PERIOD'];
                        $details->year_sap = $checkbudget['O_YEAR'];

                        if(($gl_account >= 1000) && ($gl_account < 2000) && ($gl_account != 1020))
                        {
                            $details->type_of_request = "Inventoriable";
                        }
                        else  if($gl_account == 1020)
                        {
                            $details->type_of_request = "Asset";
                        }
                        else
                        {
                            $details->type_of_request = "Direct Expense";
                        }
                }
                else
                {
                    if($request->company == 7)
                    {
                        $data = explode("-", $request->date_needed[$key]);
                        $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
                
                        $details->month_sap = date('m',strtotime($effectiveDate));
                        $details->year_sap = date('Y',strtotime($effectiveDate));
                    }
                    else
                    {
                        $data = explode("-", $request->date_needed[$key]);
                        $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
                
                        $details->month_sap =  $data[1];
                        $details->year_sap = $data[0];
                    }
                }
            }
            if($request->budget_line == null)
            {
                if($request->company == 7)
                {
                    $data = explode("-", $request->date_needed[$key]);
                    $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
                    // dd($effectiveDate);
                    $details->month_sap = date('m',strtotime($effectiveDate));
                    $details->year_sap = date('Y',strtotime($effectiveDate));
                }
                else
                {
                    $data = explode("-", $request->date_needed[$key]);
                    $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
            
                    $details->month_sap =  $data[1];
                    $details->year_sap = $data[0];
                }
            }
            
            if ($request->rio != null)
            {
                if (array_key_exists($key,$request->rio))
                {
                    $roi = $request->rio[$key];
                    $original_name = str_replace(' ', '',$roi->getClientOriginalName());
                    $name = time().'_'.$original_name;
                    
                    $roi->move(public_path().'/roi/', $name);
                    $file_name = '/roi/'.$name;
                    $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);

                    $details->roi  = $file_name ;
                }
            }
            $details->date_needed = $request->date_needed[$key];
            $details->remarks = $request->remarks[$key];
            $details->remaining_balance = $request->remaining_balance[$key];
            $details->save();
            
            $total = $total + ($request->unit_price[$key]);
        }
        if($request->attachments != null)
        {
            foreach($request->attachments as $attachment)
            {
                $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
                $name = time().'_'.$original_name;
                
                $attachment->move(public_path().'/attachment/', $name);
                $file_name = '/attachment/'.$name;
                $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
                
                $attachment = new Attachment;
                $attachment->sb_request_id        = $sb_request->id;
                $attachment->file_name  = $original_name;
                $attachment->file_url  = $file_name ;
                $attachment->save();
            }
        }

        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        if($request->company == 17)
        {
            $employee_cluster_head = $request->cluster_head;
        }
        else
        {
            $employee_cluster_head = $company->cluster_head->user_id;
        }
        $finance_head = Company::with('finance_heads')->where('id',$request->company)->first();
        if($total <= 50000.00)
        {
            $last_approver = 1922;
        }
        else
        {
            $last_approver = 1913;
        }
     

        if(($employee_supervisor->employee_head_info->user_id == $employee_bu_head->employee_head_info->user_id) && ($employee_bu_head->employee_head_info->user_id == $employee_cluster_head))
        {
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();

                $additional_approver = User::where('id',$request->general_manager)->first();
                $additional_approver->notify(new EndorseNotif($ref,$comp));
            }
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        else if($employee_bu_head->employee_head_info->user_id == $employee_cluster_head)
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    // $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
         
        }
        else
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();


            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();


            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
                    $additional_approver = User::where('id',$additional_approvers)->first();
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
        }
      

        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $employee_cluster_head;
        $request_approval->role_number = 3;
        $request_approval->status = "Pending";
        $request_approval->save();


        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $last_approver;
        $request_approval->role_number = 4;
        $request_approval->status = "Pending";
        $request_approval->save();
        $user = auth()->user();

        if($request->finance_head == null)
        {
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $finance_head->finance_heads->user_id;
            $request_approval->role_number = 5;
            $request_approval->status = "Pending";
            $request_approval->save();
    
        }
        else
        {
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $request->finance_head;
            $request_approval->role_number = 5;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        
        $request->session()->flash('status','Successfully submitted.');
        
        $user->notify(new SaveRequest($total,$ref,$comp));
        return redirect('sb-request')->with('status', 'Successfully submitted!');
    }
    public function save_refile_request(Request $request,$id)
    {
            // dd($request->all());
            $date_today_from = date('Y-m-01');
            $date_today_to = date('Y-m-d');
            $company = Company::with('cluster_head')->where('id',$request->company)->first();
              $comp = $company->company_abbreviation;
            $ref_id = SbRequest::where('company_id',$request->company)->whereDate('created_at','>=',$date_today_from)->whereDate('created_at','<=',$date_today_to)->orderBy('id','desc')->first();
            if($ref_id == null)
            {
                $ref = 1;
            }
            else
            {
                $ref = $ref_id->ref_id + 1;
            }
            $sb_request = new SbRequest;
            $sb_request->company_id = $request->company;
            // dd($request->company);
            $sb_request->department_id = $request->department;
            $sb_request->date_from_projected = $request->date_from_projected;
            $sb_request->date_to_projected = $request->date_to_projected;
            $sb_request->expected_delivery_date_from = $request->expected_delivery_date_from;
            $sb_request->expected_delivery_date_to = $request->expected_delivery_date_to;
            $sb_request->conversion_rate_used = $request->conversion_rate_used;
            $sb_request->last_status = "Pending";
            $sb_request->level = 1;
            $sb_request->ref_id = $ref;
            $sb_request->request_by = auth()->user()->id;
            $sb_request->old_request = $id;
            $sb_request->save(); 
            $total = 0;
            foreach($request->budgted as $key => $budgeted)
            {
                $date_month = explode("-",$request->date_needed[$key]);
                $details = new Detail;
                $details->sb_request_id = $sb_request->id;
                if($request->qty != null)
                {
                    if (array_key_exists($key,$request->qty))
                    {
                        $details->qty = $request->qty[$key];
                    }
                }
                if($request->no_vat != null)
                {
                    if (array_key_exists($key,$request->no_vat))
                    {
                        $details->no_vat = "Yes";
                    }
                }
                if($request->unit_price[$key] == $request->vat_inclusive[$key])
                {
                    $details->no_vat = "Yes";
                }
                if($request->type_of_request != null)
                {
                    if (array_key_exists($key,$request->type_of_request))
                    {
                        $details->type_of_request = $request->type_of_request[$key];
                    }
                }
                if($request->unit_of_measure != null)
                {
                    if (array_key_exists($key,$request->unit_of_measure))
                    {
                        $details->unit_of_measure = $request->unit_of_measure[$key];
                    }
                }
                if($request->cost_center != null)
                {
                    if (array_key_exists($key,$request->cost_center))
                    {
                        $details->cost_center = $request->cost_center[$key];
                    }
                }
                if($request->material_code != null)
                {
                    if (array_key_exists($key,$request->material_code))
                    {
                        if($request->material_code[$key] != null){

                    
                        $details->material_code = $request->material_code[$key];
                        $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company)->first();
                        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
                        $sapConnection = [
                            'ashost' => $sap_server->sapServer->app_server,
                            'sysnr' => $sap_server->sapServer->system_number,
                            'client' => $sap_server->sapServer->client,
                            'user' => $sap_user->sap_id,
                            'passwd' => $sap_user->sap_password,
                        ];
                        $check_material_description = SapApiController::executeSapFunction($sapConnection,'ZFM_GET_BASEUNIT_INTEG',       
                        [
                            'I_MATNR' => $request->material_code[$key],
                        ]
                        , null);
                        $details->unit_of_measure = $check_material_description['O_BASEUNIT'];
                        $details->material_description = $check_material_description['O_MATDESC'];

                        }
                    }
                }
                if($request->material_description != null)
                {
                    if (array_key_exists($key,$request->material_description))
                    {
                        $details->material_description = $request->material_description[$key];
                    }
                }
                if($request->io_description != null)
                {
                    if (array_key_exists($key,$request->io_description))
                    {
                        $details->io_description = $request->io_description[$key];
                    }
                }
                if($request->qty != null)
                {
                    if (array_key_exists($key,$request->qty))
                    {
                        if($request->qty[$key] != "")
                        {
                        $details->unit_price = $request->unit_price[$key]/$request->qty[$key];
                        }
                        else
                        {
                            $details->unit_price = $request->unit_price[$key];
                        }
                    }
                    else
                    {
                        $details->unit_price = $request->unit_price[$key];
                    }
                }
                else
                {
                    $details->unit_price = $request->unit_price[$key];
                }

                $details->budgeted = $request->budgted[$key];
                if($request->budget_line != null)
                {
                    if (array_key_exists($key,$request->budget_line))
                    {
                        $details->budget_code = $request->budget_line[$key];
                        $gl_account = substr($request->budget_line[$key],1,4);
                        $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company)->first();
                        // dd($sap_server); 
                        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
                        // return($sap_user);
                        $sapConnection = [
                            'ashost' => $sap_server->sapServer->app_server,
                            'sysnr' => $sap_server->sapServer->system_number,
                            'client' => $sap_server->sapServer->client,
                            'user' => $sap_user->sap_id,
                            'passwd' => $sap_user->sap_password,
                        ];
                        // return ($sapConnection);
                        $checkbudget = SapApiController::executeSapFunction($sapConnection,'ZFM_SUPBUD_INTEG',       
                        [
                            'I_AUFNR' => $request->budget_line[$key],
                            'I_PERIOD' => $date_month[1],
                            'I_YEAR' => $date_month[0],
                        ]
                        , null);
                        $data[] = $checkbudget;

                        $check_material_description = SapApiController::executeSapFunction($sapConnection,'ZFM_GET_BASEUNIT_INTEG',       
                        [
                            'I_MATNR' => $checkbudget['O_MATERIAL'],
                        ]
                        , null);
                        if($checkbudget['O_MATERIAL'] != "")
                        {

                            $details->material_code = intval($checkbudget['O_MATERIAL']);
                        }
                        else
                        {
                            $details->material_code = ($checkbudget['O_MATERIAL']);
                        }
                            // $details->gl_account = $check_material_description['O_GLACCOUNT'];
                            $details->material_description = $check_material_description['O_MATDESC'];
                            $details->unit_of_measure = $check_material_description['O_BASEUNIT'];
                            $details->io_description = $checkbudget['O_IODESC'];
                            $details->version = $checkbudget['O_VERSN'];
                            $details->order_type = $checkbudget['O_ORDRTYPE'];
                            $details->cost_center = $checkbudget['O_RESTCENTER'];
                            $details->company_code = $checkbudget['O_COMPCODE'];
                            $details->company_code = $checkbudget['O_COMPCODE'];
                            $details->month_sap = $checkbudget['O_PERIOD'];
                            $details->year_sap = $checkbudget['O_YEAR'];

                            if(($gl_account >= 1000) && ($gl_account < 2000) && ($gl_account != 1020))
                            {
                                $details->type_of_request = "Inventoriable";
                            }
                            else  if($gl_account == 1020)
                            {
                                $details->type_of_request = "Asset";
                            }
                            else
                            {
                                $details->type_of_request = "Direct Expense";
                            }

                    }
                    else
                    {
                        if($request->company == 7)
                        {
                            $data = explode("-", $request->date_needed[$key]);
                            $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
                    
                            $details->month_sap = date('m',strtotime($effectiveDate));
                            $details->year_sap = date('Y',strtotime($effectiveDate));
                        }
                        else
                        {
                            $data = explode("-", $request->date_needed[$key]);
                            $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
                    
                            $details->month_sap =  $data[1];
                            $details->year_sap = $data[0];
                        }
                    }
                }
                if($request->budget_line == null)
                {
                    if($request->company == 7)
                    {
                        $data = explode("-", $request->date_needed[$key]);
                        $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
                        // dd($effectiveDate);
                        $details->month_sap = date('m',strtotime($effectiveDate));
                        $details->year_sap = date('Y',strtotime($effectiveDate));
                    }
                    else
                    {
                        $data = explode("-", $request->date_needed[$key]);
                        $effectiveDate = date('Y-m-d', strtotime("-6 months", strtotime($request->date_needed[$key]."-01")));
                
                        $details->month_sap =  $data[1];
                        $details->year_sap = $data[0];
                    }
                }
                if ($request->roi_old != null)
                {
                        if(array_key_exists($key,$request->roi_old))
                        {

                            $details->roi  = $request->roi_old[$key];
                        }
                }
                if ($request->roi != null)
                {
                    if (array_key_exists($key,$request->rio))
                    {
                        $roi = $request->rio[$key];
                        $original_name = str_replace(' ', '',$roi->getClientOriginalName());
                        $name = time().'_'.$original_name;
                        
                        $roi->move(public_path().'/roi/', $name);
                        $file_name = '/roi/'.$name;
                        $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);

                        $details->roi  = $file_name ;
                    }
                    else 
                    {
                        if (array_key_exists($key,$request->roi_old))
                        {

                            $details->roi  = $roi ;
                        }
                    }
                   
                }
               
                $details->date_needed = $request->date_needed[$key];
                $details->remarks = $request->remarks[$key];
                $details->remaining_balance = $request->remaining_balance[$key];
                $details->save();
                $total = $total + ($request->unit_price[$key]);
            }
            if($request->attachments != null)
            {
                foreach($request->attachments as $attachment)
                {
                    $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
                    $name = time().'_'.$original_name;
                    
                    $attachment->move(public_path().'/attachment/', $name);
                    $file_name = '/attachment/'.$name;
                    $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
                    
                    $attachment = new Attachment;
                    $attachment->sb_request_id        = $sb_request->id;
                    $attachment->file_name  = $original_name;
                    $attachment->file_url  = $file_name ;
                    $attachment->save();
                }
            }
            if($request->old_attachment != null)
            {
            foreach($request->old_attachment as $attach)
            {
                $att = Attachment::where('id',$attach)->first();
                $attachment = new Attachment;
                $attachment->sb_request_id  = $sb_request->id;
                $attachment->file_name  = $att->file_name;
                $attachment->file_url  = $att->file_url;
                $attachment->save();
            }
        }
            $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
            $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
            if($request->company == 17)
        {
            $employee_cluster_head = $request->cluster_head;
        }
        else
        {
            $employee_cluster_head = $company->cluster_head->user_id;
        }
            $finance_head = Company::with('finance_heads')->where('id',$request->company)->first();
            if($total <= 50000.00)
            {
                $last_approver = 1922;
            }
            else
            {
                $last_approver = 1913;
            }


            if(($employee_supervisor->employee_head_info->user_id == $employee_bu_head->employee_head_info->user_id) && ($employee_bu_head->employee_head_info->user_id == $employee_cluster_head))
            {
                if($request->additional_approvers)
                {
                    foreach($request->additional_approvers as $additional_approvers)
                    {
                        $request_approval = new RequestApprover;
                        $request_approval->sb_request_id = $sb_request->id;
                        $request_approval->approver_id = $additional_approvers;
                        $request_approval->role_number = 0;
                        $request_approval->status = "Pending";
                        $request_approval->save();
        
                        $additional_approver = User::where('id',$additional_approvers)->first();
                        $additional_approver->notify(new EndorseNotif($ref,$comp));
                    }
                }
                if($request->general_manager)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $request->general_manager;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$request->general_manager)->first();
                    $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
                $request_approval->role_number = 1;
                $request_approval->status = "Pending";
                $request_approval->save();
    
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
                $request_approval->role_number = 2;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
            else if($employee_bu_head->employee_head_info->user_id == $employee_cluster_head)
            {
                $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
                $approver_first->notify(new ForApprovalNotif($ref,$comp));
    
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
                $request_approval->role_number = 1;
                $request_approval->status = "Pending";
                $request_approval->save();
                if($request->additional_approvers)
                {
                    foreach($request->additional_approvers as $additional_approvers)
                    {
                        $request_approval = new RequestApprover;
                        $request_approval->sb_request_id = $sb_request->id;
                        $request_approval->approver_id = $additional_approvers;
                        $request_approval->role_number = 0;
                        $request_approval->status = "Pending";
                        $request_approval->save();
        
                        $additional_approver = User::where('id',$additional_approvers)->first();
                        // $additional_approver->notify(new EndorseNotif($ref,$comp));
                    }
                }
                if($request->general_manager)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $request->general_manager;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
                }
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
                $request_approval->role_number = 2;
                $request_approval->status = "Pending";
                $request_approval->save();
             
            }
            else
            {
                $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
                $approver_first->notify(new ForApprovalNotif($ref,$comp));
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
                $request_approval->role_number = 1;
                $request_approval->status = "Pending";
                $request_approval->save();
    
    
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
                $request_approval->role_number = 2;
                $request_approval->status = "Pending";
                $request_approval->save();
    
    
                if($request->additional_approvers)
                {
                    foreach($request->additional_approvers as $additional_approvers)
                    {
                        $request_approval = new RequestApprover;
                        $request_approval->sb_request_id = $sb_request->id;
                        $request_approval->approver_id = $additional_approvers;
                        $request_approval->role_number = 0;
                        $request_approval->status = "Pending";
                        $request_approval->save();
                        $additional_approver = User::where('id',$additional_approvers)->first();
                    }
                }
                if($request->general_manager)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $request->general_manager;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
                }
            }
          

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_cluster_head;
            $request_approval->role_number = 3;
            $request_approval->status = "Pending";
            $request_approval->save();


            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $last_approver;
            $request_approval->role_number = 4;
            $request_approval->status = "Pending";
            $request_approval->save();
            $user = auth()->user();


            if($request->finance_head == null)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $finance_head->finance_heads->user_id;
                $request_approval->role_number = 5;
                $request_approval->status = "Pending";
                $request_approval->save();
        
            }
            else
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->finance_head;
                $request_approval->role_number = 5;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
            $user->notify(new SaveRequest($total,$ref,$comp));
            // $approver_first->notify(new ForApprovalNotif($ref,$comp));
            $request->session()->flash('status','Successfully submitted.');
            return redirect('sb-request')->with('status', 'Successfully submitted!');
    }
    public function cancel_request(Request $request,$request_id)
    {
        // dd($request->id);s
        $sb_request = SbRequest::where('id',$request_id)->whereHas('approvers_info', function($q){
            $q->where('status',"Pending");
        })->first();
        if($sb_request != null)
        {
        $sb_request->last_status = "Cancelled";
        $sb_request->remarks = $request->remarks;
        $sb_request->cancelled_by = auth()->user()->id;
        $sb_request->save();
        $request->session()->flash('status','Successfully cancelled.');
        return back();
        }
        else
        {
            $request->session()->flash('status','You are not allowed to cancel this Request');
            return back();
        }
       
    }
    public function approved_requests (Request $request)
    {
        $sb_requests = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')->where('last_status','Approved')
        ->where('request_by',auth()->user()->id)
        ->orderBy('created_at','asc')->get();
        return view('approved_requests',array(

            'sb_requests' => $sb_requests,
            'subheader' => 'Approved',
            'header' => 'Supplemental Budget',
        ));
    }
    public function cancel_requests (Request $request)
    {
        $sb_requests = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','cancel_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Cancelled')->orderBy('created_at','asc')->get();
        return view('cancel_requests',array(
            'sb_requests' => $sb_requests,
            'subheader' => 'Cancelled',
            'header' => 'Supplemental Budget',
        ));
    }
    public function approve_request(Request $request,$request_id)
    {
        $approve_request = RequestApprover::where('id',$request_id)->first();
        $approve_request->status = "Approved";
        $approve_request->date_action = date('Y-m-d');
        $approve_request->remarks = $request->remarks;
        if($request->attachment != null)
        {

        $attachment = $request->attachment;
        $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
        $name = time().'_'.$original_name;
        
        $attachment->move(public_path().'/attachment/', $name);
        $file_name = '/attachment/'.$name;
        $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
             $approve_request->file_path = $file_name;
        }
        $approve_request->save();
        $sb_request = SbRequest::with('company_info')->where('id',$approve_request->sb_request_id)->first();
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));
        if($approve_request->role_number != 0)
        {
            if($sb_request->type == null)
            {
                $request_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('approver_id',auth()->user()->id)->where('role_number','<',5)->orderBy('role_number','desc')->get();
            }
            else
            {
                $request_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('approver_id',auth()->user()->id)->orderBy('role_number','desc')->get();
            }
           
            if($request_approver[0]->role_number === 5)
            {
                $sb_request->last_status = "Approved";
                
            }
                $sb_request->level = $request_approver[0]->role_number + 1;
                $sb_request->save();
            foreach($request_approver as $req)
            {
                $req->status = "Approved";
                $req->date_action = date('Y-m-d');
                $req->remarks = $request->remarks;
                $req->save();
            }
            $n = $request_approver[0]->role_number + 1;
            if($n <= 5)
            {
                if($n == 2)
                {
                    $second_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',2)->first();
                    $third_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',3)->first();
                    if($second_approver->approver_id == $third_approver->approver_id)
                    {
                        $additional_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number','0')->where('status','=','Pending')->get();
                        if(count($additional_approver) == 0)
                        {
                            $next_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',$n)->first();
                            $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                            $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                        }
                        else
                        {
                            foreach($additional_approver as $add_app)
                            {
                                $add = User::where('id',$add_app->approver_id)->first();
                                $add->notify(new NextApproverNotif($ref,$comp,$date_request));
                            }
                        }
                    }
                    else
                    {
                        $next_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',$n)->first();
                        $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                        $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                    }
                }
                else if($n == 3)
                {
                    $additional_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number','0')->where('status','=','Pending')->get();
                    if(count($additional_approver) == 0)
                    {
                        $next_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',$n)->first();
                        $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                        $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                    }
                    else
                    {
                        foreach($additional_approver as $add_app)
                        {
                            $add = User::where('id',$add_app->approver_id)->first();
                            $add->notify(new NextApproverNotif($ref,$comp,$date_request));
                        }
                    }
                }
                else if ($n == 4)
                {
                        $next_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',$n)->first();
                        $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                        $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                }
                else if($n == 5)
                {
                    $next_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',$n)->first();
                        $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                        $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                }
                else
                {

                }
            }
        }
        else
        {
            $additional_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number','0')->where('status','=','Pending')->get();
            if(count($additional_approver) == 0)
            {
                $next_approver = RequestApprover::where('sb_request_id',$approve_request->sb_request_id)->where('role_number',$sb_request->level)->first();
                $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
            }
            else
            {
                foreach($additional_approver as $add_app)
                {
                    $add1 = User::where('id',$add_app->approver_id)->first();
                    $add1->notify(new NextApproverNotif($ref,$comp,$date_request));
                }
            }   
        }
        $request_by_email = User::where('id',$sb_request->request_by)->first();
        $request_by_email->notify(new ApproveRequest($ref,$comp,$date_request));
        $request->session()->flash('status','Successfully approved.');
        return back();
    }
    public function declined_request(Request $request, $request_id)
    {
        
        $dec_request = RequestApprover::where('id',$request_id)->first();
        $dec_request->status = "Declined";
        $dec_request->date_action = date('Y-m-d');
        $dec_request->remarks = $request->remarks;
        if($request->attachment != null)
        {

        $attachment = $request->attachment;
        $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
        $name = time().'_'.$original_name;
        
        $attachment->move(public_path().'/attachment/', $name);
        $file_name = '/attachment/'.$name;
        $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
             $dec_request->file_path = $file_name;
        }
        $dec_request->save();

        $sb_request = SbRequest::with('company_info')->where('id',$dec_request->sb_request_id)->first();
        $sb_request->last_status = "Cancelled";
        $sb_request->remarks = $request->remarks;
        $sb_request->reason = $request->reason;
        $sb_request->cancelled_by = auth()->user()->id;
        $sb_request->save();
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));
        $request_by_email = User::where('id',$sb_request->request_by)->first();
        $request_by_email->notify(new DeclinedRequest($ref,$comp,$date_request));
        $request->session()->flash('status','Successfully Declined.');
        return back();
    }
    public function review_again(Request $request, $request_id)
    {
        
        $dec_request = RequestApprover::where('id',$request_id)->first();
        $dec_request->status = "Review Again";
        $dec_request->date_action = date('Y-m-d');
        $dec_request->remarks = $request->remarks;
        $dec_request->save();

        $sb_request = SbRequest::with('company_info')->where('id',$dec_request->sb_request_id)->first();
        $sb_request->last_status = "Review Again";
        $sb_request->remarks = $request->remarks;
        $sb_request->reason = $request->reason;
        $sb_request->cancelled_by = auth()->user()->id;
        $sb_request->save();
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));
        $request_by_email = User::where('id',$sb_request->request_by)->first();
        $request_by_email->notify(new ReviewAgain($ref,$comp,$date_request));
        $request->session()->flash('status','Successfully tag as "Review Again".');
        return back();
    }
    public function for_finance_verification (Request $request,$request_id)
    {
        $companies = Company::orderBy('name','asc')->get();
        $departments = Department::orderBy('name','asc')->get();
        $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
        $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
       
        $approve_request = RequestApprover::where('id',$request_id)->first();
        
        $sb_request = SbRequest::with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')->where('id',$approve_request->sb_request_id)->first();
        $company_info = Company::with('company_info'
        ,'controlling_area'
        ,'order_types'
        ,'assign_letters'
        ,'finance_coors.user_info','plant_info')->where('id',$sb_request->company_id)->first();
        // dd($sb_request['details']);
        $sap_server = CompanyCode::with('sapServer')->where('company_id',$sb_request->company_id)->first();
        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
        $sapConnection = [
            'ashost' => $sap_server->sapServer->app_server,
            'sysnr' => $sap_server->sapServer->system_number,
            'client' => $sap_server->sapServer->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password,
        ];
        $check_material_description = [];
        foreach($sb_request['details'] as $details)
        {
           
            // dd($details->material_code);
            if($details->material_code != null)
            {
            $check_material_description[] = SapApiController::executeSapFunction($sapConnection,'ZFM_GET_BASEUNIT_INTEG',       
            [
                'I_MATNR' => $details->material_code,
            ]
            , null);
            }
            else
            {
                $check_material_description[] = null;
            }
        }
        $cost_centers = SapApiController::executeSapFunction($sapConnection,'ZFM_COSTCENTER_LIST',       
        [
            'COMPANY_CODE' => $sap_server->name,
            // 'DATE' => $date,
        ]
        , null);
        return view('verification_finance',array(
            'subheader' => 'For approval finance',
            'header' => 'Supplemental Budget',
            'companies' => $companies,
            'departments' => $departments,
            'unit_of_measures' => $unit_of_measures,
            'employee_company' => $employee_company,
            'employee_supervisor' => $employee_supervisor,
            'employee_bu_head' => $employee_bu_head,
            'employee_cluster_head' => $employee_cluster_head,
            'sb_request' => $sb_request,
            'company_info' => $company_info,
            'cost_centers' => $cost_centers,
            'check_material_description' => $check_material_description,
        ));
    }
    public function save_verify (Request $request, $request_id)
    {
        // dd($request->all());
        $approve_request = RequestApprover::where('sb_request_id',$request_id)->where('role_number',5)->first();
        $approve_request->status = "Approved";
        $approve_request->date_action = date('Y-m-d');
        $approve_request->remarks = $request->remarks;
        if($request->attachment != null)
        {

        $attachment = $request->attachment;
        $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
        $name = time().'_'.$original_name;
        
        $attachment->move(public_path().'/attachment/', $name);
        $file_name = '/attachment/'.$name;
        $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
        $approve_request->file_path = $file_name;
        
        }
        $approve_request->save();

        $sb_request = SbRequest::where('id',$approve_request->sb_request_id)->first();
        if($sb_request->level == 5)
        {
            $sb_request->last_status = "Approved";
            $sb_request->save();
        }
        foreach($request->remaining_balance as $key => $remaining_balance)
        {
            $details = Detail::where('id',$key)->first();
            $details->remaining_balance = $remaining_balance;
            $details->material_code = $request->material_code[$key];
            $details->material_description = $request->material_description[$key];
          
            if($request->order_type != null)
            {
            if (array_key_exists($key,$request->order_type))
            {
                $details->order_type = $request->order_type[$key];
            }
            if (array_key_exists($key,$request->company_code))
            {
                $details->company_code = $request->company_code[$key];
            }
            if (array_key_exists($key,$request->cost_center))
            {
                $details->cost_center = $request->cost_center[$key];
            }
            if (array_key_exists($key,$request->gl_account))
            {
                $details->gl_account = $request->gl_account[$key];
            }
            if (array_key_exists($key,$request->gl_account))
            {
                $details->gl_account = $request->gl_account[$key];
            }
            if (array_key_exists($key,$request->plant))
            {
                $details->plant = $request->plant[$key];
                $business_area = Plant::where('company_id',$sb_request->company_id)->where('plant',$request->plant[$key])->first();
                $details->business_area = $business_area->business_area;
            }
            }
            $details->controlling_area = $request->controlling_area[$key];
            $details->version = $request->version[$key];
            $details->io_description = $request->io_description[$key];
            $details->budget_code = $request->budget_line[$key];
            $details->save();
        }
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));

        $request_by_email = User::where('id',$sb_request->request_by)->first();
        $request_by_email->notify(new ApproveRequest($ref,$comp,$date_request));
        $request->session()->flash('status','Successfully verified.');
        return redirect('sb-for-approval-finance')->with('status', 'Successfully Verified!');
    }
    public function for_upload (Request $request)
    {
        // dd(auth()->user()->role_info());
        $co = (auth()->user()->company_info())->pluck('company_id');
        // dd($co);
        // dd($co->toArray());        
        $co = $co->toArray();
        // dd($co);
        $companies = Company::whereHas('controlling_area')->whereIn('id',$co)->orderBy('name','asc')->get();
        $comp = $request->company;
        
        if($comp == null)
        {
            $sup_details = Detail::whereHas('sb_request',function ($query) {
                $query->where('last_status','=',"Approved")
                ->where('type','=',null);    
            })
            ->whereHas('approvers',function ($query) {
                $query->where('approver_id','=',auth()->user()->id) 
                ->where('role_number','=',5);  
            })
            ->where('date_uploaded_budget',"=",null)->get();

        
            $create_ios = Detail::whereHas('sb_request',function ($query) {
                $query->where('last_status','=',"Approved")
                ->where('type','=',null);   
            })
            ->whereHas('approvers',function ($query) {
                $query->where('approver_id','=',auth()->user()->id) 
                ->where('role_number','=',5);  
            })
            ->where('create_io_date',"=",null)->where('budgeted',"=","Not Budgeted")->get();
            $sup_details_ids = $sup_details->pluck('id');
            $create_ios_id = $create_ios->pluck('id');
            // dd($sup_details);
        
        }
        else
        {
            $sup_details = Detail::whereHas('sb_request',function ($query) use ($comp) {
                $query->where('last_status','=',"Approved")
                ->where('company_id','=',$comp)
                ->where('type','=',null);    
            })
            ->whereHas('approvers',function ($query)  {
                $query->where('approver_id','=',auth()->user()->id) 
                ->where('role_number','=',5);  
            })
            ->where('date_uploaded_budget',"=",null)->get();
    
            // dd($sup_details);
          
            $create_ios = Detail::whereHas('sb_request',function ($query) use ($comp) {
                $query->where('last_status','=',"Approved")
                ->where('company_id','=',$comp)
                ->where('type','=',null);   
            })
            ->whereHas('approvers',function ($query) {
                $query->where('approver_id','=',auth()->user()->id) 
                ->where('role_number','=',5);  
            })
            ->where('create_io_date',"=",null)->where('budgeted',"=","Not Budgeted")->get();
            $sup_details_ids = $sup_details->pluck('id');
            $create_ios_id = $create_ios->pluck('id');
            
        }

        return view('upload_budget',array(
            'subheader' => 'For upload',
            'header' => 'Supplemental Budget',
            'sup_details' => $sup_details,
            'create_ios' => $create_ios,
            'sup_details_ids' => $sup_details_ids,
            'create_ios_id' => $create_ios_id,
            'companies' => $companies,
            'comp' => $comp,
        ));
    }
    public function for_upload_reallocation (Request $request)
    {
        $co = (auth()->user()->company_info())->pluck('company_id');
        // dd($co);
        // dd($co->toArray());        
        $co = $co->toArray();
        // dd($co);
        $companies = Company::whereHas('controlling_area')->whereIn('id',$co)->orderBy('name','asc')->get();
        $comp = $request->company;
        if($comp == null)
        {
            $sup_details = ReallocationDetail::whereHas('sb_request',function ($query)  {
                $query->where('last_status','=',"Approved");
                // ->where('type','!=','Non SAP');    
            })
            ->whereHas('approvers',function ($query) {
                $query->where('approver_id','=',auth()->user()->id) 
                ->where('role_number','=',4);  
            })
            ->where('date_uploaded_budget',null)->get();
            $sup_details_ids = $sup_details->pluck('id');
        }
        else
        {
            $sup_details = ReallocationDetail::whereHas('sb_request',function ($query) use ($comp) {
                $query->where('last_status','=',"Approved")
                ->where('company_id','=',$comp);
                // ->where('type','!=','Non SAP');    
            })
            ->whereHas('approvers',function ($query) {
                $query->where('approver_id','=',auth()->user()->id) 
                ->where('role_number','=',4);  
            })
            ->where('date_uploaded_budget',null)->get();
            $sup_details_ids = $sup_details->pluck('id');
        }
        // dd($sup_details);
        return view('for_upload_reallocation',array(
            'subheader' => 'For Upload',
            'header' => 'Reallocation Request',
            'sup_details' => $sup_details,
            'sup_details_ids' => $sup_details_ids,
            'companies' => $companies,
            'comp' => $comp,
        ));
    }
    public function for_upload_non_sap (Request $request)
    {
        $sup_details = Detail::whereHas('sb_request',function ($query) {
            $query->where('last_status','=',"Approved")
            ->where('type','=','Non SAP');  
        })
        ->whereHas('approvers',function ($query) {
            $query->where('approver_id','=',auth()->user()->id) 
            ->where('role_number','=',5);  
        })
        ->with('sb_request.company_info','sb_request.user_info')
        ->get();
        
        return view('upload_budget_non_sap',array(
            'subheader' => 'Approved Request Non-SAP',
            'header' => 'Supplemental Budget',
            'sup_details' => $sup_details,
        ));
    }
    public function download_upload_budget(Request $request)
    {

        $array = $request->ids;
        // dd(count($array));
        for($i = 0 ; $i < count($array);$i++)
        {
            // dump($array[$i]);
            $id = intval($array[$i]); 
            // dd($id);
            $a = Detail::findOrfail($id);
            // dd(auth()->user()->id);
            $a->date_uploaded_budget = date('Y-m-d');
            $a->budget_upload_by = auth()->user()->id;
            $a->save();
        }
         return "Sucess";
        // foreach($array as $arr)
        // {
        //     $detail = Detail::where('id',$arr)->first();
        //     $datail->date_uploaded_budget = date('Y-m-d');
        //     $datail->budget_upload_by = auth()->user()->id;
        //     $datail->save();
    
        // }
        
        // return "success";
    }
    public function down_reallocation(Request $request)
    {

        $array = $request->ids;
        $abb = explode(",", $array[0]);
        // dd(($a));
        for($i = 0 ; $i < count($abb);$i++)
        {
            // return 1;
            $id = intval(str_replace("[","",$abb[$i])); 
            $id = intval(str_replace("]","",$id)); 
            // dd($id);
            $a = ReallocationDetail::findOrfail($id);
            $a->date_uploaded_budget = date('Y-m-d');
            $a->budget_upload_by = auth()->user()->id;
            $a->save();

        }
         return "Sucess";
    }
    public function download_upload_io(Request $request)
    {

        $array = $request->ids;
        // dd(count($array));
        for($i = 0 ; $i < count($array);$i++)
        {
            // dump($array[$i]);
            $id = intval($array[$i]); 
            // dd($id);
            $a = Detail::findOrfail($id);
            // dd(auth()->user()->id);
            $a->create_io_date = date('Y-m-d');
            $a->create_io_by = auth()->user()->id;
            $a->save();
        }
         return "Sucess";
     
    }
    public function get_info(Request $request)
    {
        // $data = [];
        if($request->value == null)
        {
            $year = date('Y');
            $month = date('m');
        }
        else
        {
            
            $date_month = explode("-",$request->value);
            // dd($date_month);
            $year = $date_month[0];
            $month = $date_month[1];
            
        }
        // dd($request->value);
        $gl_account = substr($request->io,1,4);
        $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company_id)->first();
        $code = $sap_server->name;
        $controlling_area = ControllingArea::where('company_id',$request->company_id)->first();
        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
        // return($sap_user);
        $sapConnection = [
            'ashost' => $sap_server->sapServer->app_server,
            'sysnr' => $sap_server->sapServer->system_number,
            'client' => $sap_server->sapServer->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password,
        ];
        // return ($sapConnection);
        $checkbudget = SapApiController::executeSapFunction($sapConnection,'ZFM_SUPBUD_INTEG',       
        [
            'I_AUFNR' => $request->io,
            'I_PERIOD' => $month,
            'I_YEAR' => $year,
            // 'I_DATE' => date('Ymd'),
        ]
        , null);
        $data[] = $checkbudget;
        $check_material_description = SapApiController::executeSapFunction($sapConnection,'ZFM_GET_BASEUNIT_INTEG',       
        [
            'I_MATNR' => $checkbudget['O_MATERIAL'],
        ]
        , null);
        $gl_acc = intval($checkbudget['O_GLACCOUNT']);
        $payroll = Payroll::where('company_code',$code)->where('gl_account',$gl_acc)->first();
        $plant = Plant::where('plant',$checkbudget['O_PLANT'])->first();
        // dd($payroll);
        $data[] = $check_material_description;
        $data[] = $gl_account;
        $data[] = $controlling_area->controlling_area;
        $data[] = $code;
        $data[] = $payroll;
        if($plant != null)
        {
            $data[] = $plant->approver_id;
            $data[] = $plant->no_vat;
            $data[] = $plant->cluster_head;
        }
        else
        {
            $data[] = $plant;
            $data[] = $plant;
            $data[] = $plant;
        }
        return ($data);
      
    }
    public function get_material_info(Request $request)
    {
        // dd($request->all());
        $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company_id)->first();
        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
        // dd($sap_server);
        $sapConnection = [
            'ashost' => $sap_server->sapServer->app_server,
            'sysnr' => $sap_server->sapServer->system_number,
            'client' => $sap_server->sapServer->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password,
        ];
        // dd($sapConnection);
        $check_material_description = SapApiController::executeSapFunction($sapConnection,'ZFM_GET_BASEUNIT_INTEG',       
        [
            'I_MATNR' => $request->io,
        ]
        , null);
        // dd($check_material_description);
        return $check_material_description;

    }
    public function get_infor(Request $request)
    {
        $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company_id)->first();
        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
   
        $sapConnection = [
            'ashost' => $sap_server->sapServer->app_server,
            'sysnr' => $sap_server->sapServer->system_number,
            'client' => $sap_server->sapServer->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password,
        ];
        // dd($sap_server);
        if($request->gl_account != null)
        {
            $gl_account = str_pad($request->gl_account, 10, "0", STR_PAD_LEFT);
        }
        else
        {
            $gl_account = "";
        }
        if($request->cost_center != null)
        {
            $cost_center = str_pad($request->cost_center, 10, "0", STR_PAD_LEFT);
        }
        else
        {
            $cost_center = "";
        }
        $check_material_description = SapApiController::executeSapFunction($sapConnection,'ZFM_NEWIO_INTEG',       
        [
            'I_COMPCODE' => $sap_server->name,
            'I_GLACC' => $gl_account,
            'I_COSTCEN' => $cost_center,
            'I_UNIT' => "",
        ]
        , null);
        // dd($check_material_description);
        return $check_material_description;
    }
    public function get_cost_center (Request $request)  
    {
        $sap_server = CompanyCode::with('sapServer')->where('company_id',$request->company_id)->first();
        // dd($sap_server);
        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
   
        $sapConnection = [
            'ashost' => $sap_server->sapServer->app_server,
            'sysnr' => $sap_server->sapServer->system_number,
            'client' => $sap_server->sapServer->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password,
        ];
        // dd($sapConnection);
        $date = date('m/d/Y');
        $cost_center = SapApiController::executeSapFunction($sapConnection,'ZFM_COSTCENTER_LIST',       
        [
            'COMPANY_CODE' => $sap_server->name,
            // 'DATE' => $date,
        ]
        , null);
        // dd($check_material_description);
        return $cost_center['COSTCENTER_LIST'];

    }   
    public function reportspercompany(Request $request)
    {
        
        $companies = Company::with('total','total_approved.details','total_declined')->get();
        // $reasons = Reason::orderBy('reason','asc')->get();
        return view('reports_per_company',array(

            // 'sb_requests' => $sb_requests,
            // 'reasons' => $reasons,
            'companies' => $companies,
            'subheader' => 'Company',
            'header' => 'Reports',
        ));
    }
    public function reportsperdepartment(Request $request)
    {
        
        $departments = Department::orderBy('name','asc')->get();
        // $reasons = Reason::orderBy('reason','asc')->get();
        return view('reports_per_department',array(

            // 'sb_requests' => $sb_requests,
            // 'reasons' => $reasons,
            'departments' => $departments,
            'subheader' => 'Department',
            'header' => 'Reports',
        ));
    }
    public function reportperuser(Request $request)
    {
        $co = (auth()->user()->company_info())->pluck('company_id');
        // dd($co->toArray());        
        $co = $co->toArray();
        // dd($co);
        $companies = Company::whereHas('controlling_area')->whereIn('id',$co)->orderBy('name','asc')->get();
        // $companies = Company::whereHas('controlling_area')->orderBy('name','asc')->get();
        $comp = $request->company;
        $date_select = $request->month;
        $users = SbRequest::with('user_info')->with('SbRequestData')
        ->where('company_id',$request->company)->whereHas('details',function ($query) use ($date_select) {
            // $query->where('last_status','=',"Approved")
            // $query->where('date_needed','=',$date_select);
        })
        ->where('last_status','=',"Approved")
        ->select('request_by')
        ->groupBy('request_by')
        ->get('request_by');
        // dd($users);
        return view('report_per_user',array(

            // 'sb_requests' => $sb_requests,
            // 'reasons' => $reasons,
            'companies' => $companies,
            'comp' => $comp,
            // 'cost_center' => $cost_center,
            'date_select' => $date_select,
            'users' => $users,
            'subheader' => 'User',
            'header' => 'Reports',
        ));
       
        // $reasons = Reason::orderBy('reason','asc')->get();
        return view('reports_per_department',array(

            // 'sb_requests' => $sb_requests,
            // 'reasons' => $reasons,
            'departments' => $departments,
            'subheader' => 'Department',
            'header' => 'Reports',
        ));
    }
    public function sb_request_nonsap(Request $request)
    { 

        $currencies = Currency::get();
        // dd($currencies);
       
        $companies = Company::whereDoesntHave('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
        $departments = Department::orderBy('name','asc')->get();
        $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
        $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
        // dd($employee_supervisor->employee_head_id);
        $employees = AssignHead::where('employee_head_id','!=',$employee_supervisor->employee_head_id)
        ->where('employee_head_id','!=',$employee_bu_head->employee_head_id)
        ->where('employee_head_id','!=',$employee_cluster_head->employee_head_id)
        ->whereHas('employee_head_info',function ($query) 
        {
             $query->where('status', '=', "Active");
        })
        ->where('head_id','=',4)
        ->orWhere('head_id','=',5)
        // ->orWhere('head_id','=',4)
        ->groupBy('employee_head_id')->get(['employee_head_id']);
        // dd($employees[1]);
        return view('new_sb_non_sap',array(
            'subheader' => 'Request',
            'header' => 'Supplemental Budget',
            'companies' => $companies,
            'departments' => $departments,
            'unit_of_measures' => $unit_of_measures,
            'employee_company' => $employee_company,
            'employee_supervisor' => $employee_supervisor,
            'employee_bu_head' => $employee_bu_head,
            'employee_cluster_head' => $employee_cluster_head,
            'currencies' => $currencies,
            'employees' => $employees,
        ));

    }
    public function save_request_non_sap(Request $request)
    {
        // dd($request->all());
        $date_today_from = date('Y-m-01');
        $date_today_to = date('Y-m-d');
        $ref_id = SbRequest::where('company_id',$request->company)->whereDate('created_at','>=',$date_today_from)->whereDate('created_at','<=',$date_today_to)->orderBy('id','desc')->first();
        $company = Company::with('cluster_head')->where('id',$request->company)->first();
        $comp = $company->company_abbreviation;
        // dd($request->all());
        if($ref_id == null)
        {
            $ref = 1;
        }
        else
        {
            $ref = $ref_id->ref_id + 1;
        }
        $sb_request = new SbRequest;
        $sb_request->company_id = $request->company;
        $sb_request->department_id = $request->department;
        // $sb_request->project_disbursement_date = $request->projected_disbursement_date;
        $sb_request->date_from_projected = $request->date_from_projected;
        $sb_request->date_to_projected = $request->date_to_projected;
        $sb_request->expected_delivery_date_from = $request->expected_delivery_date_from;
        $sb_request->expected_delivery_date_to = $request->expected_delivery_date_to;
        $sb_request->conversion_rate_used = $request->conversion_rate_used;
        $sb_request->last_status = "Pending";
        $sb_request->level = 1;
        $sb_request->ref_id = $ref;
        $sb_request->type = "Non SAP";
        $sb_request->request_by = auth()->user()->id;
        $sb_request->save(); 
        $total = 0;
        foreach($request->type_of_request as $key => $type_of_request)
        {
            $date_month = explode("-",$request->date_needed[$key]);
            
            $details = new Detail;
            if($request->no_vat != null)
            {
                if (array_key_exists($key,$request->no_vat))
                {
                    $details->no_vat = "Yes";
                }
            }
          
            $details->sb_request_id = $sb_request->id;
            if($request->qty != null)
            {
                if (array_key_exists($key,$request->qty))
                {
                    $details->qty = $request->qty[$key];
                    $details->unit_price = $request->unit_price[$key]/$request->qty[$key];
                }
                else
                {
                    $details->unit_price = $request->unit_price[$key];
                }
              
            }
            if($request->cost_center != null)
            {
                if (array_key_exists($key,$request->cost_center))
                {
                    $details->cost_center = $request->cost_center[$key];
                }
            
              
            }

            if($request->type_of_request != null)
            {
                if (array_key_exists($key,$request->type_of_request))
                {
                    $details->type_of_request = $request->type_of_request[$key];
                }
            }
            if($request->unit_of_measure != null)
            {
                if (array_key_exists($key,$request->unit_of_measure))
                {
                    $details->unit_of_measure = $request->unit_of_measure[$key];
                }
            }
            
        
            if($request->material_description != null)
            {
                if (array_key_exists($key,$request->material_description))
                {
                    $details->material_description = $request->material_description[$key];
                }
            }
            if($request->io_description != null)
            {
                if (array_key_exists($key,$request->io_description))
                {
                    $details->io_description = $request->io_description[$key];
                }
            }
      
            if ($request->rio != null)
            {
                if (array_key_exists($key,$request->rio))
                {
                    $roi = $request->rio[$key];
                    $original_name = str_replace(' ', '',$roi->getClientOriginalName());
                    $name = time().'_'.$original_name;
                    
                    $roi->move(public_path().'/roi/', $name);
                    $file_name = '/roi/'.$name;
                    $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);

                    $details->roi  = $file_name ;
                }
            }
            $details->date_needed = $request->date_needed[$key];
            $details->remarks = $request->remarks[$key];
            $details->remaining_balance = $request->remaining_balance[$key];
            $details->save();

            $total = $total + ( $request->unit_price[$key]);
        }
        if($request->attachments != null)
        {
            foreach($request->attachments as $attachment)
            {
                $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
                $name = time().'_'.$original_name;
                
                $attachment->move(public_path().'/attachment/', $name);
                $file_name = '/attachment/'.$name;
                $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
                
                $attachment = new Attachment;
                $attachment->sb_request_id        = $sb_request->id;
                $attachment->file_name  = $original_name;
                $attachment->file_url  = $file_name ;
                $attachment->save();
            }
        }

        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = $company->cluster_head->user_id;
        $finance_head = Company::with('finance_heads')->where('id',$request->company)->first();
        if($total <= 50000.00)
        {
            $last_approver = 1922;
        }
        else
        {
            $last_approver = 1913;
        }
        if(($employee_supervisor->employee_head_info->user_id == $employee_bu_head->employee_head_info->user_id) == ($employee_bu_head->employee_head_info->user_id == $employee_cluster_head))
        {
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();

                $additional_approver = User::where('id',$request->general_manager)->first();
                $additional_approver->notify(new EndorseNotif($ref,$comp));
            }
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        else if($employee_bu_head->employee_head_info->user_id == $employee_cluster_head)
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    // $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
         
        }
        else
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();


            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();


            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
                    $additional_approver = User::where('id',$additional_approvers)->first();
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
        }
      

        
        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $employee_cluster_head;
        $request_approval->role_number = 3;
        $request_approval->status = "Pending";
        $request_approval->save();

        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $last_approver;
        $request_approval->role_number = 4;
        $request_approval->status = "Pending";
        $request_approval->save();
        $user = auth()->user();

        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $finance_head->finance_heads->user_id;
        $request_approval->role_number = 5;
        $request_approval->status = "Pending";
        $request_approval->save();

       
        $user->notify(new SaveRequest($total,$ref,$comp));
        $request->session()->flash('status','Successfully submitted.');
        return redirect('sb-request')->with('status', 'Successfully submitted!');

    }
    public function reportspercostcenter(Request $request)
    {
        // dd(auth()->user()->company_info());
        $co = (auth()->user()->company_info())->pluck('company_id');
        // dd($co->toArray());        
        $co = $co->toArray();
        // dd($co);
        $companies = Company::whereHas('controlling_area')->whereIn('id',$co)->orderBy('name','asc')->get();
        
        // $reasons = Reason::orderBy('reason','asc')->get();
        $comp = $request->company;
        $date_select = $request->month;
        $cost_center = [];
        $total = [];

        if($request->company)
        {
            $sap_server = CompanyCode::with('sapServer')->where('company_id',$comp)->first();
            // dd($sap_server);
            $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
       
            $sapConnection = [
                'ashost' => $sap_server->sapServer->app_server,
                'sysnr' => $sap_server->sapServer->system_number,
                'client' => $sap_server->sapServer->client,
                'user' => $sap_user->sap_id,
                'passwd' => $sap_user->sap_password,
            ];
            // dd($sapConnection);
            // $date = date('m/d/Y');
            $cost_center = SapApiController::executeSapFunction($sapConnection,'ZFM_COSTCENTER_LIST',       
            [
                'COMPANY_CODE' => $sap_server->name,
                // 'DATE' => $date,
            ]
            , null);
            $cost_center = $cost_center['COSTCENTER_LIST'];
        }
        foreach($cost_center as $cost)
        {
            // dd();
            $detail = Detail::where('cost_center','=',$cost->COSTCENTER)->where('date_needed','=',$request->month)
            ->whereHas('sb_request',function ($query) {
                $query->where('last_status','=',"Approved")
                ->where('type','=',null);
            })
            // ->sum('')
            ->get();
            // dd($detail);
            $total[] = $detail;
        }
        return view('report_per_cost_center',array(

            // 'sb_requests' => $sb_requests,
            // 'reasons' => $reasons,
            'companies' => $companies,
            'comp' => $comp,
            'cost_center' => $cost_center,
            'date_select' => $date_select,
            'subheader' => 'Cost Center',
            'header' => 'Reports',
        ));
    }
    public function re_alloc(Request $request)
    {

        //  dd($company_info);
        $companies = Company::whereHas('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
      
        $sb_requests = ReAllocation::with('user_info','company_info','department_info','details','approvers_info.employee_info','approvers_info.user_info','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Pending')
        ->orderBy('created_at','asc')
        ->get();
        // dd($sb_requests);

        $companies = Company::whereHas('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
       
        return view('re_alloc',array(

            'sb_requests' => $sb_requests,
            'companies' => $companies,
            'subheader' => 'Request',
            'header' => 'Reallocation Request',
        ));
    }
    public function re_alloc_request (Request $request)
    {
        $comp = $request->company;
        $r = $request->req;
        $company_info = Company::with('controlling_area','general_info.user_info','cluster_head.user_info')->where('id',$comp)->orderBy('name','asc')->first();
        $cluster_heads = Plant::with('cluster_head_info')->where('company_id',$comp)->groupBy('cluster_head')->get(['cluster_head']);
        $cluster_heads_id = ($cluster_heads->pluck('cluster_head'))->toArray();
        $companies = Company::whereHas('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
        $departments = Department::orderBy('name','asc')->get();
        $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
        $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
        // dd($employee_supervisor->employee_head_id);
        $employees = Endorsement::with('employee_info')->get();
        return view('re_alloc_new_request',array(
            'subheader' => 'Request',
            'header' => 'Reallocation Request',
            'companies' => $companies,
            'departments' => $departments,
            'unit_of_measures' => $unit_of_measures,
            'employee_company' => $employee_company,
            'employee_supervisor' => $employee_supervisor,
            'employee_bu_head' => $employee_bu_head,
            'employee_cluster_head' => $employee_cluster_head,
            'company_info' => $company_info,
            'employees' => $employees,
            'cluster_heads' => $cluster_heads,
            'cluster_heads_id' => $cluster_heads_id,
            'r' => $r,
        ));
    }
    public function save_realloc (Request $request)
    {
        $date_today_from = date('Y-m-01');
        $date_today_to = date('Y-m-d');
        $ref_id = ReAllocation::where('company_id',$request->company)->whereDate('created_at','>=',$date_today_from)->whereDate('created_at','<=',$date_today_to)->orderBy('id','desc')->first();
        $company = Company::with('cluster_head')->where('id',$request->company)->first();
        $comp = $company->company_abbreviation;
        if($ref_id == null)
        {
            $ref = 1;
        }
        else
        {
            $ref = $ref_id->ref_id + 1;
        }
        $reallocation = new ReAllocation;
        $reallocation->company_id = $request->company;
        $reallocation->department_id = $request->department;
        $reallocation->request_by = auth()->user()->id;
        $reallocation->last_status = "Pending";
        $reallocation->level = 1;
        $reallocation->ref_id = $ref;
        $reallocation->save();

        foreach($request->budget_line as $key => $budget_line)
        {
            $detail = new ReallocationDetail;
            $detail->re_allocations_id = $reallocation->id;
            $detail->budget_code = $budget_line;
            $detail->qty = $request->qty[$key];
            $detail->amount = $request->total_amount[$key];
            $detail->date_from = $request->date_from[$key];
            $detail->date_to = $request->date_to[$key];
            $detail->remarks = $request->reason[$key];
            $detail->version_from = $request->version_from[$key];
            $detail->version_to = $request->version_to[$key];
            $detail->unit_of_measure = $request->unit_of_measure[$key];
            $detail->controlling_area = $request->controlling_area[$key];
            $detail->budget_description = $request->io_description[$key];
            $effectiveDate_from = date('Y-m-d', strtotime("-6 months", strtotime($request->date_from[$key]."-01")));
            $effectiveDate_to = date('Y-m-d', strtotime("-6 months", strtotime($request->date_to[$key]."-01")));
            if($request->company == 7)
            {
                $detail->month_sap_from = date('m',strtotime($effectiveDate_from));
                $detail->year_sap_from = date('Y',strtotime($effectiveDate_from));
                $detail->month_sap_to = date('m',strtotime($effectiveDate_to));
                $detail->year_sap_to =  date('Y',strtotime($effectiveDate_to));
            }
            else
            {
                $detail->month_sap_from = date('m',strtotime($request->date_from[$key]."-01"));
            $detail->year_sap_from = date('Y',strtotime($request->date_from[$key]."-01"));
            $detail->month_sap_to = date('m',strtotime($request->date_to[$key]."-01"));
            $detail->year_sap_to =  date('Y',strtotime($request->date_to[$key]."-01"));
            }
            
            $detail->save();
        }

        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        if($request->company == 17)
        {
            $employee_cluster_head = $request->cluster_head;
        }
        else
        {
            $employee_cluster_head = $company->cluster_head->user_id;
        }
        $finance_head = Company::with('finance_heads')->where('id',$request->company)->first();
        
        if(($employee_supervisor->employee_head_info->user_id == $employee_bu_head->employee_head_info->user_id) && ($employee_bu_head->employee_head_info->user_id == $employee_cluster_head))
        {
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new ReallocationApprover;
                    $request_approval->reallocation_id = $reallocation->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
          
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();

            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        else if($employee_bu_head->employee_head_info->user_id == $employee_cluster_head)
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));

            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();

            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new ReallocationApprover;
                    $request_approval->reallocation_id = $reallocation->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    // $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
          
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
         
        }
        else
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));

            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();


            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();


            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new ReallocationApprover;
                    $request_approval->reallocation_id = $reallocation->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
                    $additional_approver = User::where('id',$additional_approvers)->first();
                }
            }
        }
        $request_approval = new ReallocationApprover;
        $request_approval->reallocation_id = $reallocation->id;
        $request_approval->approver_id = $employee_cluster_head;
        $request_approval->role_number = 3;
        $request_approval->status = "Pending";
        $request_approval->save();

        if($request->finance_head == null)
        {
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $finance_head->finance_heads->user_id;
            $request_approval->role_number = 4;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        else
        {
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $request->finance_head;
            $request_approval->role_number = 4;
            $request_approval->status = "Pending";
            $request_approval->save();
        }

        if($request->attachments != null)
        {
            foreach($request->attachments as $attachment)
            {
                $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
                $name = time().'_'.$original_name;
                
                $attachment->move(public_path().'/attachment/', $name);
                $file_name = '/attachment/'.$name;
                $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
                
                $attachment = new ReallocationAttachment;
                $attachment->re_allocation_id = $reallocation->id;
                $attachment->file_name  = $original_name;
                $attachment->file_url  = $file_name ;
                $attachment->save();
            }
        }
        $user = auth()->user();
        $user->notify(new RequestReallocation($ref,$comp));
        $request->session()->flash('status','Successfully submitted.');
        return redirect('re-request')->with('status', 'Successfully submitted!');
    }
    public function save_re_alloc_refile (Request $request,$id)
    {
        // dd($request->all());
        $date_today_from = date('Y-m-01');
        $date_today_to = date('Y-m-d');
        $ref_id = ReAllocation::where('company_id',$request->company)->whereDate('created_at','>=',$date_today_from)->whereDate('created_at','<=',$date_today_to)->orderBy('id','desc')->first();
        $company = Company::with('cluster_head')->where('id',$request->company)->first();
        $comp = $company->company_abbreviation;
        if($ref_id == null)
        {
            $ref = 1;
        }
        else
        {
            $ref = $ref_id->ref_id + 1;
        }
        $reallocation = new ReAllocation;
        $reallocation->company_id = $request->company;
        $reallocation->department_id = $request->department;
        $reallocation->request_by = auth()->user()->id;
        $reallocation->last_status = "Pending";
        $reallocation->level = 1;
        $reallocation->old_request = $id;
        $reallocation->ref_id = $ref;
        $reallocation->save();

        foreach($request->budget_line as $key => $budget_line)
        {
            $detail = new ReallocationDetail;
            $detail->re_allocations_id = $reallocation->id;
            $detail->budget_code = $budget_line;
            $detail->qty = $request->qty[$key];
            $detail->amount = $request->total_amount[$key];
            $detail->date_from = $request->date_from[$key];
            $detail->date_to = $request->date_to[$key];
            $detail->remarks = $request->reason[$key];
            $detail->version_from = $request->version_from[$key];
            $detail->version_to = $request->version_to[$key];
            $detail->unit_of_measure = $request->unit_of_measure[$key];
            $detail->controlling_area = $request->controlling_area[$key];
            $detail->budget_description = $request->io_description[$key];
            $effectiveDate_from = date('Y-m-d', strtotime("-6 months", strtotime($request->date_from[$key]."-01")));
            $effectiveDate_to = date('Y-m-d', strtotime("-6 months", strtotime($request->date_to[$key]."-01")));
            if($request->company == 7)
            {
                $detail->month_sap_from = date('m',strtotime($effectiveDate_from));
                $detail->year_sap_from = date('Y',strtotime($effectiveDate_from));
                $detail->month_sap_to = date('m',strtotime($effectiveDate_to));
                $detail->year_sap_to =  date('Y',strtotime($effectiveDate_to));
            }
            else
            {
                $detail->month_sap_from = date('m',strtotime($request->date_from[$key]."-01"));
            $detail->year_sap_from = date('Y',strtotime($request->date_from[$key]."-01"));
            $detail->month_sap_to = date('m',strtotime($request->date_to[$key]."-01"));
            $detail->year_sap_to =  date('Y',strtotime($request->date_to[$key]."-01"));
            }
            
            $detail->save();
        }

        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = $company->cluster_head->user_id;
        $finance_head = Company::with('finance_heads')->where('id',$request->company)->first();
        
        if(($employee_supervisor->employee_head_info->user_id == $employee_bu_head->employee_head_info->user_id) && ($employee_bu_head->employee_head_info->user_id == $employee_cluster_head))
        {
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new ReallocationApprover;
                    $request_approval->reallocation_id = $reallocation->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
          
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();

            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        else if($employee_bu_head->employee_head_info->user_id == $employee_cluster_head)
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));
            
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new ReallocationApprover;
                    $request_approval->reallocation_id = $reallocation->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    // $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
          
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
         
        }
        else
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));
            
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();


            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();


            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new ReallocationApprover;
                    $request_approval->reallocation_id = $reallocation->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
                    $additional_approver = User::where('id',$additional_approvers)->first();
                }
            }
        }
        $request_approval = new ReallocationApprover;
        $request_approval->reallocation_id = $reallocation->id;
        $request_approval->approver_id = $employee_cluster_head;
        $request_approval->role_number = 3;
        $request_approval->status = "Pending";
        $request_approval->save();

        if($request->finance_head == null)
        {
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $finance_head->finance_heads->user_id;
            $request_approval->role_number = 4;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        else
        {
            $request_approval = new ReallocationApprover;
            $request_approval->reallocation_id = $reallocation->id;
            $request_approval->approver_id = $request->finance_head;
            $request_approval->role_number = 4;
            $request_approval->status = "Pending";
            $request_approval->save();
        }

        if($request->old_attachment != null)
        {
            foreach($request->old_attachment as $old_attach)
            {
                $att = ReallocationAttachment::where('id',$old_attach)->first();
                $attachment = new ReallocationAttachment;
                $attachment->re_allocation_id = $reallocation->id;
                $attachment->file_name  = $att->file_name;
                $attachment->file_url  = $att->file_url ;
                $attachment->save();
            }
        }
        if($request->attachments != null)
        {
            foreach($request->attachments as $attachment)
            {
                $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
                $name = time().'_'.$original_name;
                
                $attachment->move(public_path().'/attachment/', $name);
                $file_name = '/attachment/'.$name;
                $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
                
                $attachment = new ReallocationAttachment;
                $attachment->re_allocation_id = $reallocation->id;
                $attachment->file_name  = $original_name;
                $attachment->file_url  = $file_name ;
                $attachment->save();
            }
        }
        $user = auth()->user();
        $user->notify(new RequestReallocation($ref,$comp));
        $request->session()->flash('status','Successfully submitted.');
        return redirect('re-request')->with('status', 'Successfully submitted!');
    }
    public function cancel_request_alloc(Request $request,$id)
    {
        // dd('renz');
        $sb_request = Reallocation::where('id',$id)->first();
        $sb_request->last_status = "Cancelled";
        $sb_request->remarks = $request->remarks;
        $sb_request->cancelled_by = auth()->user()->id;
        $sb_request->save();
        $request->session()->flash('status','Successfully cancelled.');
        return back();
    }
    public function cancel_requests_reallocation(Request $request)
    {
        $sb_requests = Reallocation::with('user_info','company_info','department_info','approvers_info.user_info','cancel_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Cancelled')->orderBy('created_at','asc')->get();
        $companies = Company::whereHas('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
      
        return view('cancel_requests_reallocation',array(
            'sb_requests' => $sb_requests,
            'subheader' => 'Cancelled',
            'header' => 'Reallocation Request',
        ));
    }
    public function approved_requests_reallocation (Request $request)
    {
        $sb_requests = Reallocation::with('user_info','company_info','department_info','approvers_info.user_info','cancel_info','details','attachments')
        ->where('request_by',auth()->user()->id)
        ->where('last_status','Approved')->orderBy('created_at','asc')->get();
        return view('approved_request_reallocations',array(
            'sb_requests' => $sb_requests,
            'subheader' => 'Approved',
            'header' => 'Reallocation Request',
        ));
    }
    public function re_for_approval(Request $request)
    {
        $sb_requests = ReallocationApprover::with(
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
        // dd($sb_requests);
        $reasons = Reason::orderBy('reason','asc')->get();
        return view('reallocation_for_approval',array(

            'sb_requests' => $sb_requests,
            'reasons' => $reasons,
            'subheader' => 'For approval',
            'header' => 'Reallocation Request',
        ));
    }
    public function re_reallocation_declined(Request $request, $request_id)
    {
        
        $dec_request = ReallocationApprover::where('id',$request_id)->first();
        $dec_request->status = "Declined";
        $dec_request->date_action = date('Y-m-d');
        $dec_request->remarks = $request->remarks;
        $dec_request->save();

        $sb_request = Reallocation::where('id',$dec_request->reallocation_id)->first();
        $sb_request->last_status = "Cancelled";
        $sb_request->remarks = $request->remarks;
        $sb_request->reason = $request->reason;
        $sb_request->cancelled_by = auth()->user()->id;
        $sb_request->save();

        $request->session()->flash('status','Successfully Declined.');
        return back();
    }
    public function re_file_sup_budget(Request $request,$id)
    {

        $sb_reject_request = SbRequest::where('id',$id)->first();
        // dd($sb_reject_request);
        $finance_approver = RequestApprover::where('sb_request_id',$id)->where('role_number',5)->first();
        $cluster_head = RequestApprover::where('sb_request_id',$id)->where('role_number',3)->first();
        
        $r = $request->req;
        $emp = [];
        $company = $sb_reject_request->company_id;
        $company_info = Company::with('controlling_area','general_info.user_info','plant_info','cluster_head.user_info')->where('id',$company)->orderBy('name','asc')->first();
        //  dd($company_info);
        if($company_info->controlling_area != null)
        {
            $sap_server = CompanyCode::with('sapServer')->where('company_id',$sb_reject_request->company_id)->first();
        $sap_user = SapUser::where('user_id', 0)->where('sap_server', $sap_server->sap_server)->first();
        $sapConnection = [
            'ashost' => $sap_server->sapServer->app_server,
            'sysnr' => $sap_server->sapServer->system_number,
            'client' => $sap_server->sapServer->client,
            'user' => $sap_user->sap_id,
            'passwd' => $sap_user->sap_password,
        ];
        $cost_centers = SapApiController::executeSapFunction($sapConnection,'ZFM_COSTCENTER_LIST',       
        [
            'COMPANY_CODE' => $sap_server->name,
            // 'DATE' => $date,
        ]
        , null);
        $currencies = Currency::get();
        $companies = Company::whereHas('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
        $cluster_heads = Plant::with('cluster_head_info')->where('company_id',$company)->groupBy('cluster_head')->get(['cluster_head']);
        $cluster_heads_id = ($cluster_heads->pluck('cluster_head'))->toArray();
        $departments = Department::orderBy('name','asc')->get();
        $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
        $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
        // dd($employee_supervisor->employee_head_id);
        $employees = Endorsement::with('employee_info')->get();
        // dd($employees[1]);
        return view('sb_request_new_refile',array(
            'subheader' => 'Request',
            'header' => 'Supplemental Budget',
            'companies' => $companies,
            'sb_reject_request' => $sb_reject_request,
            'departments' => $departments,
            'unit_of_measures' => $unit_of_measures,
            'employee_company' => $employee_company,
            'employee_supervisor' => $employee_supervisor,
            'employee_bu_head' => $employee_bu_head,
            'employee_cluster_head' => $employee_cluster_head,
            'currencies' => $currencies,
            'employees' => $employees,
            'company_info' => $company_info,
            'cost_centers' => $cost_centers,
            'r' => $r,
            'finance_approver' => $finance_approver,
            'cluster_heads' => $cluster_heads,
            'cluster_heads_id' => $cluster_heads_id,
            'cluster_head' => $cluster_head,
        ));
        }
        else
        {
            $currencies = Currency::get();
            // dd($currencies);
            
            $companies = Company::whereDoesntHave('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
            $departments = Department::orderBy('name','asc')->get();
            $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
            $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
            $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
            $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
            $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
            // dd($employee_supervisor->employee_head_id);
            $employees = Endorsement::with('employee_info')->get();
            $cost_centers = CostCenter::get();
            // dd($employees[1]);
            // $employees = $employees->toArray();
            // dd($employees);
            return view('new_sb_non_sap_refile',array(
                'subheader' => 'Request',
                'header' => 'Supplemental Budget',
                'companies' => $companies,
                'sb_reject_request' => $sb_reject_request,
                'departments' => $departments,
                'unit_of_measures' => $unit_of_measures,
                'employee_company' => $employee_company,
                'employee_supervisor' => $employee_supervisor,
                'employee_bu_head' => $employee_bu_head,
                'employee_cluster_head' => $employee_cluster_head,
                'currencies' => $currencies,
                'employees' => $employees,
                'company_info' => $company_info,
                'cost_centers' => $cost_centers,
                'r' => $r,
            ));
        }
    }
    public function reallocation_approved(Request $request, $id)
    {
        $approve_request = ReallocationApprover::where('id',$id)->first();
        // dd($approve_request);
        $approve_request->status = "Approved";
        $approve_request->date_action = date('Y-m-d');
        $approve_request->remarks = $request->remarks;
      
       
        $approve_request->save();
        $sb_request = ReAllocation::with('company_info')->where('id',$approve_request->reallocation_id)->first();
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));
        if($approve_request->role_number != 0)
        {
            $request_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('approver_id',auth()->user()->id)->orderBy('role_number','desc')->get();
        
            if($request_approver[0]->role_number === 4)
            {
                $sb_request->last_status = "Approved";
            }
            else
            {
                $sb_request->level = $request_approver[0]->role_number + 1;
            }
            foreach($request_approver as $req)
            {
                $req->status = "Approved";
                $req->date_action = date('Y-m-d');
                $req->remarks = $request->remarks;
                $req->save();
            }
            $n = $request_approver[0]->role_number + 1;
            if($n <= 4)
            {
                if($n == 2)
                {
                    $second_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number',2)->first();
                    $third_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number',3)->first();
                    if($second_approver->approver_id == $third_approver->approver_id)
                    {
                        $additional_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number','0')->where('status','=','Pending')->get();
                        if(count($additional_approver) == 0)
                        {
                            $next_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number',$n)->first();
                            $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                            $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                        }
                        else
                        {
                            foreach($additional_approver as $add_app)
                            {
                                $add = User::where('id',$add_app->approver_id)->first();
                                $add->notify(new NextApproverNotif($ref,$comp,$date_request));
                            }
                        }
                    }
                    else
                    {
                        $next_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number',$n)->first();
                        $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                        $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                    }
                }
                else if($n == 3)
                {
                    $additional_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number','0')->where('status','=','Pending')->get();
                    if(count($additional_approver) == 0)
                    {
                        $next_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number',$n)->first();
                        $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                        $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                    }
                    else
                    {
                        foreach($additional_approver as $add_app)
                        {
                            $add = User::where('id',$add_app->approver_id)->first();
                            $add->notify(new NextApproverNotif($ref,$comp,$date_request));
                        }
                    }
                }
                else if($n==4)
                {
                    $next_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number',$n)->first();
                    $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                    $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
                }
                else
                {

                }
            }
            $sb_request->save();
           
        }
        else
        {
            $additional_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number','0')->where('status','=','Pending')->get();
            // dd(count($additional_approver));
            if(count($additional_approver) == 0)
            {   
                $next_approver = ReallocationApprover::where('reallocation_id',$approve_request->reallocation_id)->where('role_number',$sb_request->level)->first();
                $next_approver_email = User::where('id',$next_approver->approver_id)->first();
                $next_approver_email->notify(new NextApproverNotif($ref,$comp,$date_request));
            }
            else
            {
                foreach($additional_approver as $add_app)
                {
                    $add1 = User::where('id',$add_app->approver_id)->first();
                    $add1->notify(new NextApproverNotif($ref,$comp,$date_request));
                }
            }   
        }
        $request->session()->flash('status','Successfully approved.');
        return back();
    }
    public function budget_codes (Request $request)
    {
        $companies = Company::whereDoesntHave('controlling_area')->with('finance_heads.user_info','company_info')->orderBy('name','asc')->get();

        //dd($companies);
       
        $unit_of_measures = UnitOfMeasure::get();
        // dd($general_managers);
        $cost_centers = CostCenter::orderBy('dept_name')->get();
        $departments = Department::orderBy('name','asc')->get();
        $budget_codes = BudgetCode::with('company_info')->orderBy('company_id')->get();
        return view('budget_codes',array(
            'companies' => $companies,
            'cost_centers' => $cost_centers,
            'unit_of_measures' => $unit_of_measures,
            'budget_codes' => $budget_codes,
            'subheader' => 'Budget Codes',
            'header' => 'Settings',
        ));
    }
    public function approved_history(Request $request)
    {
        $sb_requests = SbRequest:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Approved');
           
        })->with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments')
        ->orderBy('created_at','asc')
        ->get();

        $re_allocations = ReAllocation:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Approved');
        })->with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')
      
        ->orderBy('created_at','asc')
        ->get();
        return view('approved_request_history',array(

            'sb_requests' => $sb_requests,
            're_allocations' => $re_allocations,
            'subheader' => 'Approved Request History',
            'header' => 'Action History',
        ));
    }
    public function review_again_history(Request $request)
    {
        $sb_requests = SbRequest:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Review Again');
           
        })
        ->where('last_status','=','Review Again')
        ->with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments')
        ->orderBy('created_at','asc')
        ->get();

       
        return view('review_again_history',array(

            'sb_requests' => $sb_requests,
            // 're_allocations' => $re_allocations,
            'subheader' => 'Review Again History',
            'header' => 'Action History',
        ));
    }
    public function declined_history(Request $request)
    {
        $sb_requests = SbRequest:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Declined');
           
        })->with('user_info','company_info','department_info','approvers_info.user_info','approvers_info.employe_info','details','attachments')
        ->orderBy('created_at','asc')
        ->get();

        $re_allocations = ReAllocation:: whereHas('approvers_info', function ($query)  {
            $query->where('approver_id',auth()->user()->id)
                ->where('status','=','Declined');
        })->with('user_info','company_info','department_info','approvers_info.user_info','details','attachments')
      
        ->orderBy('created_at','asc')
        ->get();

        return view('declined_request_history',array(
            'sb_requests' => $sb_requests,
            're_allocations' => $re_allocations,
            'subheader' => 'Declined Request History',
            'header' => 'Action History',
        ));

    }
    public function manual_email_follow_up(Request $request)
    {
        $request_a = RequestApprover::where('id',$request->approver_id)->first();
        $sb_request = SbRequest::where('id',$request_a->sb_request_id)->first();
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));
        $approver_first = User::where('id',$request_a->approver_id)->first();
        $approver_first->notify(new NextApproverNotif($ref,$comp,$date_request));
        return $request_a;
    }
    public function manual_email_follow_up_re_alloc(Request $request)
    {
        $request_a = ReallocationApprover::where('id',$request->approver_id)->first();
        $sb_request = ReAllocation::where('id',$request_a->reallocation_id)->first();
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));
        $approver_first = User::where('id',$request_a->approver_id)->first();
        $approver_first->notify(new NextApproverNotif($ref,$comp,$date_request));
        return $request_a;
    }
    public function save_refile_request_non_sap (Request $request,$id)
    {
        $date_today_from = date('Y-m-01');
        $date_today_to = date('Y-m-d');
        $ref_id = SbRequest::where('company_id',$request->company)->whereDate('created_at','>=',$date_today_from)->whereDate('created_at','<=',$date_today_to)->orderBy('id','desc')->first();
        $company = Company::with('cluster_head')->where('id',$request->company)->first();
        $comp = $company->company_abbreviation;
        // dd($request->all());
        if($ref_id == null)
        {
            $ref = 1;
        }
        else
        {
            $ref = $ref_id->ref_id + 1;
        }
        $sb_request = new SbRequest;
        $sb_request->company_id = $request->company;
        $sb_request->department_id = $request->department;
        // $sb_request->project_disbursement_date = $request->projected_disbursement_date;
        $sb_request->date_from_projected = $request->date_from_projected;
        $sb_request->date_to_projected = $request->date_to_projected;
        $sb_request->expected_delivery_date_from = $request->expected_delivery_date_from;
        $sb_request->expected_delivery_date_to = $request->expected_delivery_date_to;
        $sb_request->conversion_rate_used = $request->conversion_rate_used;
        $sb_request->last_status = "Pending";
        $sb_request->level = 1;
        $sb_request->ref_id = $ref;
        $sb_request->old_request = $id;
        $sb_request->type = "Non SAP";
        $sb_request->request_by = auth()->user()->id;
        $sb_request->save(); 
        $total = 0;
        foreach($request->type_of_request as $key => $type_of_request)
        {
            $date_month = explode("-",$request->date_needed[$key]);
        
            $details = new Detail;
            $details->sb_request_id = $sb_request->id;
            if($request->no_vat != null)
            {
                if (array_key_exists($key,$request->no_vat))
                {
                    $details->no_vat = "Yes";
                }
            }
            if($request->qty != null)
            {
                if (array_key_exists($key,$request->qty))
                {
                    $details->qty = $request->qty[$key];
                    $details->unit_price = $request->unit_price[$key]/$request->qty[$key];
                }
                else
                {
                    $details->unit_price = $request->unit_price[$key];
                }
              
            }
            if($request->cost_center != null)
            {
                if (array_key_exists($key,$request->cost_center))
                {
                    $details->cost_center = $request->cost_center[$key];
                }
            
              
            }

            if($request->type_of_request != null)
            {
                if (array_key_exists($key,$request->type_of_request))
                {
                    $details->type_of_request = $request->type_of_request[$key];
                }
            }
            if($request->unit_of_measure != null)
            {
                if (array_key_exists($key,$request->unit_of_measure))
                {
                    $details->unit_of_measure = $request->unit_of_measure[$key];
                }
            }
            
        
            if($request->material_description != null)
            {
                if (array_key_exists($key,$request->material_description))
                {
                    $details->material_description = $request->material_description[$key];
                }
            }
            if($request->io_description != null)
            {
                if (array_key_exists($key,$request->io_description))
                {
                    $details->io_description = $request->io_description[$key];
                }
            }
      
            if ($request->roi_old != null)
            {
                    if(array_key_exists($key,$request->roi_old))
                    {

                        $details->roi  = $roi ;
                    }
            }
            if ($request->rio != null)
            {
                if (array_key_exists($key,$request->rio))
                {
                    $roi = $request->rio[$key];
                    $original_name = str_replace(' ', '',$roi->getClientOriginalName());
                    $name = time().'_'.$original_name;
                    
                    $roi->move(public_path().'/roi/', $name);
                    $file_name = '/roi/'.$name;
                    $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);

                    $details->roi  = $file_name ;
                }
                else 
                {
                    if (array_key_exists($key,$request->roi_old))
                    {

                        $details->roi  = $roi ;
                    }
                }
               
            }
           
            $details->date_needed = $request->date_needed[$key];
            $details->remarks = $request->remarks[$key];
            $details->remaining_balance = $request->remaining_balance[$key];
            $details->save();
            $total = $total + ( $request->unit_price[$key]);
        }
        if($request->attachments != null)
        {
            foreach($request->attachments as $attachment)
            {
                $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
                $name = time().'_'.$original_name;
                
                $attachment->move(public_path().'/attachment/', $name);
                $file_name = '/attachment/'.$name;
                $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);
                
                $attachment = new Attachment;
                $attachment->sb_request_id        = $sb_request->id;
                $attachment->file_name  = "NEW".$original_name;
                $attachment->file_url  = $file_name ;
                $attachment->save();
            }
        }
        if($request->old_attachment != null)
        {
            foreach($request->old_attachment as $attach)
            {
                $att = Attachment::where('id',$attach)->first();
                $attachment = new Attachment;
                $attachment->sb_request_id  = $sb_request->id;
                $attachment->file_name  = $att->file_name;
                $attachment->file_url  = $att->file_url;
                $attachment->save();
            }
        }
        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = $company->cluster_head->user_id;
        $finance_head = Company::with('finance_heads')->where('id',$request->company)->first();
        if($total <= 50000.00)
        {
            $last_approver = 1922;
        }
        else
        {
            $last_approver = 1913;
        }
        if(($employee_supervisor->employee_head_info->user_id == $employee_bu_head->employee_head_info->user_id) && ($employee_bu_head->employee_head_info->user_id == $employee_cluster_head))
        {
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();

                $additional_approver = User::where('id',$request->general_manager)->first();
                $additional_approver->notify(new EndorseNotif($ref,$comp));
            }
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
        }
        else if($employee_bu_head->employee_head_info->user_id == $employee_cluster_head)
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();
            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
    
                    $additional_approver = User::where('id',$additional_approvers)->first();
                    // $additional_approver->notify(new EndorseNotif($ref,$comp));
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();
         
        }
        else
        {
            $approver_first = User::where('id',$employee_supervisor->employee_head_info->user_id)->first();
            $approver_first->notify(new ForApprovalNotif($ref,$comp));

            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_supervisor->employee_head_info->user_id;
            $request_approval->role_number = 1;
            $request_approval->status = "Pending";
            $request_approval->save();


            $request_approval = new RequestApprover;
            $request_approval->sb_request_id = $sb_request->id;
            $request_approval->approver_id = $employee_bu_head->employee_head_info->user_id;
            $request_approval->role_number = 2;
            $request_approval->status = "Pending";
            $request_approval->save();


            if($request->additional_approvers)
            {
                foreach($request->additional_approvers as $additional_approvers)
                {
                    $request_approval = new RequestApprover;
                    $request_approval->sb_request_id = $sb_request->id;
                    $request_approval->approver_id = $additional_approvers;
                    $request_approval->role_number = 0;
                    $request_approval->status = "Pending";
                    $request_approval->save();
                    $additional_approver = User::where('id',$additional_approvers)->first();
                }
            }
            if($request->general_manager)
            {
                $request_approval = new RequestApprover;
                $request_approval->sb_request_id = $sb_request->id;
                $request_approval->approver_id = $request->general_manager;
                $request_approval->role_number = 0;
                $request_approval->status = "Pending";
                $request_approval->save();
            }
        }
            
        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $employee_cluster_head;
        $request_approval->role_number = 3;
        $request_approval->status = "Pending";
        $request_approval->save();

        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $last_approver;
        $request_approval->role_number = 4;
        $request_approval->status = "Pending";
        $request_approval->save();

        $request_approval = new RequestApprover;
        $request_approval->sb_request_id = $sb_request->id;
        $request_approval->approver_id = $finance_head->finance_heads->user_id;
        $request_approval->role_number = 5;
        $request_approval->status = "Pending";
        $request_approval->save();
     
        $user = auth()->user();
        $user->notify(new SaveRequest($total,$ref,$comp));
        $request->session()->flash('status','Successfully submitted.');
        return redirect('sb-request')->with('status', 'Successfully submitted!');

    }
    public function re_file_re_alloc (Request $request,$id)
    {
        $compa = ReAllocation::with('user_info','company_info','department_info','details','approvers_info.employee_info','approvers_info.user_info','attachments')->where('id',$id)->first();
        $r = $request->req;
        $cluster_heads = Plant::with('cluster_head_info')->where('company_id',$compa->company_id)->groupBy('cluster_head')->get(['cluster_head']);
        $cluster_heads_id = ($cluster_heads->pluck('cluster_head'))->toArray();
        $company_info = Company::with('controlling_area','general_info.user_info','cluster_head.user_info')->where('id',$compa->company_id)->orderBy('name','asc')->first();
        $finance_approver = ReallocationApprover::where('reallocation_id',$id)->where('role_number',4)->first();
        $cluster_head = ReallocationApprover::where('reallocation_id',$id)->where('role_number',3)->first();
       
        $companies = Company::whereHas('controlling_area')->whereHas('finance_heads')->orderBy('name','asc')->get();
        $departments = Department::orderBy('name','asc')->get();
        $unit_of_measures = UnitOfMeasure::orderBy('name','asc')->get();
        $employee_company = Employee::with('EmployeeCompany','EmployeeDepartment')->where('user_id','=',auth()->user()->id)->first();
        $employee_supervisor = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',3)->first();
        $employee_bu_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',4)->first();
        $employee_cluster_head = AssignHead::with('employee_head_info')->where('employee_id',auth()->user()->employee_info()->id)->where('head_id',5)->first();
        // dd($employee_supervisor->employee_head_id);
        $employees = Endorsement::with('employee_info')->get();
        return view('re_allocation_re_file',array(
            'subheader' => 'Request',
            'header' => 'Reallocation Request',
            'companies' => $companies,
            'departments' => $departments,
            'unit_of_measures' => $unit_of_measures,
            'employee_company' => $employee_company,
            'employee_supervisor' => $employee_supervisor,
            'employee_bu_head' => $employee_bu_head,
            'employee_cluster_head' => $employee_cluster_head,
            'company_info' => $company_info,
            'employees' => $employees,
            'compa' => $compa,
            'finance_approver' => $finance_approver,
            'cluster_heads' => $cluster_heads,
            'cluster_heads_id' => $cluster_heads_id,
            'cluster_head' => $cluster_head,
            'r' => $r,
        ));
    }
    public function reply_review_again(Request $request,$id)
    {
        $sb_request = SbRequest::with('company_info')->findOrfail($id);
        $ref = $sb_request->ref_id;
        $comp = $sb_request->company_info->company_abbreviation;
        $date_request = date('Ym',strtotime($sb_request->created_at));
        $approver_id = RequestApprover::where('sb_request_id',$sb_request->id)->where('status','=','Review Again')->first();
        $additional_remarks = new RequestApproverRemark;
        $additional_remarks->request_approver_id = $approver_id->id;
        $additional_remarks->remarks = $sb_request->remarks;
        $additional_remarks->reason = $sb_request->reason;
        $additional_remarks->user_id = $approver_id->approver_id;
        $additional_remarks->status = "Review Again";
        $additional_remarks->date_action = $approver_id->date_action;
        $additional_remarks->save();
        $approver_id->status = "Pending";
        $sb_request->last_status = "Pending";
        $sb_request->save();
        $approver_id->save();
        $file_name = null;

        if($request->attachment != null)
        {

        $attachment = $request->attachment;
        $original_name = str_replace(' ', '',$attachment->getClientOriginalName());
        $name = time().'_'.$original_name;
        
        $attachment->move(public_path().'/attachment/', $name);
        $file_name = '/attachment/'.$name;
        $ext = pathinfo(storage_path().$file_name, PATHINFO_EXTENSION);

        }

        $additional_remarks = new RequestApproverRemark;
        $additional_remarks->request_approver_id = $approver_id->id;
        $additional_remarks->remarks = $request->remarks;
        $additional_remarks->user_id = auth()->user()->id;
        $additional_remarks->date_action = date('Y-m-d');
        $additional_remarks->file_path  = $file_name;
        $additional_remarks->save();

        $next_approver_email = User::where('id',$approver_id->approver_id)->first();
        $next_approver_email->notify(new ReplyReviewAgain($ref,$comp,$date_request));

        $request->session()->flash('status','Successfully submitted.');
        return back();
    }
    public function planned_budget_vs_spent(Request $request)
    {
        
    }
    
}

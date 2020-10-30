<?php

namespace App\Http\Controllers;
use App\Company;
use App\ClusterHead;
use App\Employee;
use Illuminate\Http\Request;

class ClusterHeadController extends Controller
{
    //
    public function view_cluster_heads()
    {
        $companies = Company::with('finance_heads.user_info','company_info','cluster_head.user_info','approver_plant.cluster_head_info')
        ->whereHas('finance_heads')
        ->orderBy('name','asc')
        ->get();
        // dd($companies);
        $employees = Employee::where('status','=','Active')->orderBy(TRIM('first_name'),'asc')->get();
        return view('cluster_heads',array(
            'companies' => $companies,
            'employees' => $employees,
            'subheader' => 'Cluster Head',
            'header' => 'Settings',
        ));
    }
    public function edit_cluster_head(Request $request,$id)
    {
        $company_head = ClusterHead::where('company_id',$id)->delete();
        // $company_code = CompanyCode::where('company_id',$company_id)->delete();
        // $controlling_area = ControllingArea::where('company_id',$company_id)->delete();
        // $order_type = OrderType::where('company_id',$company_id)->delete();
        // $assign_letter = AssignLetter::where('company_id',$company_id)->delete();
        // $company_coor = CompanyFinanceCoor::where('company_id',$company_id)->delete();

        $company_head = new ClusterHead;
        $company_head->company_id = $id;
        $company_head->user_id = $request->finance_head;
        $company_head->added_by = auth()->user()->id;
        $company_head->save();
        $request->session()->flash('status','Successfully submitted.');
        return back(); 
    }

}

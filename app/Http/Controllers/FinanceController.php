<?php

namespace App\Http\Controllers;
use App\Company;
use App\LastApprover;
use App\CompanyFinanceHead;
use App\CompanyFinanceCoor;
use App\Employee;
use App\CompanyCode;
use App\AssignLetter;
use App\OrderType;
use App\ControllingArea;
use App\UnitOfMeasure;
use App\Department;
use App\GeneralManager;
use App\Endorsement;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    //
    public function finance_view ()
    {
        $companies = Company::with('finance_heads.user_info','finance_coors.user_info','company_info'
        ,'controlling_area'
        ,'order_types'
        ,'assign_letters'
        ,'finance_coors.user_info','plant_info')->orderBy('name','asc')->get();

        //dd($companies);
        $last_approvers = LastApprover::with('user_info')->get();
        $employees = Employee::where('status','=','Active')->orderBy(TRIM('first_name'),'asc')->get();
        $unit_of_measures = UnitOfMeasure::get();
        $general_managers = GeneralManager::with('company_info','user_info')->get();
        $endorsements = Endorsement::with('department_info','user_info','employee_info.EmployeeCompany')->get();
        // dd($general_managers);
        $departments = Department::orderBy('name','asc')->get();
        return view('finance_maintenance',array(
            'companies' => $companies,
            'employees' => $employees,
            'last_approvers' => $last_approvers,
            'unit_of_measures' => $unit_of_measures,
            'general_managers' => $general_managers,
            'departments' => $departments,
            'endorsements' => $endorsements,
            'subheader' => 'Finance',
            'header' => 'Settings',
        ));
    }
    public function finance_edit(Request $request,$company_id)
    {
        // dd($request->all());
        $company_head = CompanyFinanceHead::where('company_id',$company_id)->delete();
        // $company_code = CompanyCode::where('company_id',$company_id)->delete();
        // $controlling_area = ControllingArea::where('company_id',$company_id)->delete();
        // $order_type = OrderType::where('company_id',$company_id)->delete();
        // $assign_letter = AssignLetter::where('company_id',$company_id)->delete();
        // $company_coor = CompanyFinanceCoor::where('company_id',$company_id)->delete();

        $company_head = new CompanyFinanceHead;
        $company_head->company_id = $company_id;
        $company_head->user_id = $request->finance_head;
        $company_head->created_by = auth()->user()->id;
        $company_head->save();

        // foreach($request->finance_coor as $finance_coor)
        // {
        //     $company_head = new CompanyFinanceCoor;
        //     $company_head->company_id = $company_id;
        //     $company_head->user_id = $finance_coor;
        //     $company_head->created_by = auth()->user()->id;
        //     $company_head->save();
        // }
        // $company_code = new CompanyCode;
        // $company_code->name = $request->company_code;
        // $company_code->company_id = $request->company_id;
        // $company_code->created_by = auth()->user()->id;
        // $company_code->save();

        // $controlling_area = new ControllingArea;
        // $controlling_area->controlling_area = $request->controlling_area;
        // $controlling_area->company_id = $request->company_id;
        // $controlling_area->created_by = auth()->user()->id;
        // $controlling_area->save();

        // foreach($request->order_types as $order_type)
        // {
        //     $order_types = new OrderType;
        //     $order_types->order_type = $order_type;
        //     $order_types->company_id = $request->company_id;
        //     $order_types->created_by = auth()->user()->id;
        //     $order_types->save();
        // }

        // foreach($request->assign_characters as $assign_character)
        // {
        //     $assign_characters = new AssignLetter;
        //     $assign_characters->assign_letter = $assign_character;
        //     $assign_characters->company_id = $request->company_id;
        //     $assign_characters->created_by = auth()->user()->id;
        //     $assign_characters->save();
        // }
       

        $request->session()->flash('status','Successfully submitted.');
        return back(); 
    }
    public function new_unit_of_measure(Request $request)
    {
            $uom = new UnitOfMeasure;
            $uom->name = $request->unit_of_measure;
            $uom->save();
            $request->session()->flash('status','Successfully submitted.');
            return back();
    }
    public function edit_unit_of_measure(Request $request, $uom_id)
    {
            $uom = UnitOfMeasure::where('id',$uom_id)->first();
            $uom->name = $request->unit_of_measure;
            $uom->save();
            $request->session()->flash('status','Successfully submitted.');
            return back();
    }
    public function edit_approver(Request $request, $id)
    {

        // dd($request->all());
            $last_approver = LastApprover::where('id',$id)->first();
            $last_approver->user_id = $request->employee;
            $last_approver->sign = $request->sign;
            $last_approver->amount = $request->amount;
            $last_approver->save();
            $request->session()->flash('status','Successfully submitted.');
            return back();
    }
    public function new_coo (Request $request)
    {
        
        $coo = new GeneralManager;
        $coo->company_id = $request->company;
        $coo->user_id = $request->employee;
        $coo->add_by = auth()->user()->id;
        $coo->save();
        $request->session()->flash('status','Successfully submitted.');
        return back();
    }
    public function new_endorsement  (Request $request)
    {
        // dd($request->all());
        $coo = new Endorsement;
        $coo->department_id = $request->company;
        $coo->user_id = $request->employee;
        $coo->save();
        $request->session()->flash('status','Successfully submitted.');
        return back();
    }
}

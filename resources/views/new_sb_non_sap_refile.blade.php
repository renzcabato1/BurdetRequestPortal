@extends('layouts.header')

@section('content')
@if(session()->has('status'))
<div class="alert alert-success alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{session()->get('status')}}
</div>
@endif
<br>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                Reference Number : {{$sb_reject_request->company_info->company_abbreviation}}-{{date('Ym',strtotime($sb_reject_request->created_at))}}-{{str_pad($sb_reject_request->ref_id, 4, '0', STR_PAD_LEFT)}} <br>
                Reason :  {{$sb_reject_request->reason}}  <br>
                Remarks :  {{$sb_reject_request->remarks}}  <br>
                <hr>
                <label class="font-normal">Immediate Supervisor <span style='color:red;'>*</span> :  @if($employee_supervisor) {{$employee_supervisor->employee_head_info->first_name." ".$employee_supervisor->employee_head_info->last_name}} @else <i> Note : Please contact HR to edit your supervisor </i> @endif </label><br>
                <label class="font-normal">BU Head <span style='color:red;'>*</span> : @if($employee_bu_head) {{$employee_bu_head->employee_head_info->first_name." ".$employee_bu_head->employee_head_info->last_name}} @else <i> Note : Please contact HR to edit your BU Head </i> @endif </label><br>
                <label class="font-normal">Cluster Head <span style='color:red;'>*</span> :  @if($company_info->cluster_head)  {{$company_info->cluster_head->user_info->name}} @else <i> Note : Please place clusterhead at masterlist. </i> @endif </label><br>
               
                {{-- {{dd($r)}} --}}
                
                <form  method='POST' id='myform' action='save-sb-request-non-sap-refile/{{$sb_reject_request->id}}' onsubmit='show();' enctype="multipart/form-data"  >
                    {{ csrf_field() }}
                    @if($company_info->general_info != null)
                        @if($company_info->general_info->user_id != auth()->user()->id)
                            @if($company_info->general_info->user_id != $employee_supervisor->employee_head_info->user_id)
                                @if($company_info->general_info->user_id != $employee_bu_head->employee_head_info->user_id)
                                    @if($company_info->general_info->user_id != $company_info->cluster_head->user_id)
                                    {{-- {{dd($r)}} --}}
                                    <div class="form-group">
                                        <div class='row'>
                                            <div class='col-lg-6'>
                                                <label class="font-normal">General Manager / COO<span style='color:red;'>*</span></label>
                                                <select data-placeholder="Choose Approver" name='general_manager' class="chosen-select "  tabindex="1"  required>
                                                    <option value="{{$company_info->general_info->user_id}}">{{$company_info->general_info->user_info->name}}</option>
                                                    {{-- @foreach($employees as $employee)
                                                        <option  value="{{$employee->employee_info->user_id}}">{{$employee->employee_info->first_name}} {{$employee->employee_info->last_name}} - ({{$employee->employee_info->position}})</option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endif
                    {{-- @if($r == "b") --}}
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-lg-6'>
                                <label class="font-normal">Additional Approver <span style='color:red;'>*</span></label>
                                <select data-placeholder="Choose Approver" name='additional_approvers[]' class="chosen-select "  tabindex="1" multiple required>
                                    @foreach($employees as $employee)
                                        @if($employee_supervisor->employee_head_info->user_id != $employee->employee_info->user_id)
                                            @if($employee_bu_head->employee_head_info->user_id != $employee->employee_info->user_id)
                                                @if($company_info->cluster_head->user_id != $employee->employee_info->user_id)
                                                    <option  value="{{$employee->employee_info->user_id}}">{{$employee->employee_info->first_name}} {{$employee->employee_info->last_name}} - {{$employee->position}} </option>
                                                @endif
                                            @endif    
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- @endif --}}
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-lg-3'>
                                <label class="font-normal">Company <span style='color:red;'>*</span></label>
                                <select data-placeholder="Choose Company" name='company' class="chosen-select "   id='company_id' required="true" tabindex="1" required>
                                    <option value="{{$company_info->id}}">{{$company_info->name}}</option>
                                    {{-- @foreach($companies as $company)
                                    <option  value="{{$company->id}}" @if($employee_company->EmployeeCompany) {{($company->id == $employee_company->EmployeeCompany[0]->id) ? "selected":"" }} @endif>{{$company->name}}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class='col-lg-3'>
                                <label class="font-normal">Department <span style='color:red;'>*</span></label>
                                <select data-placeholder="Choose Department" name='department' class="chosen-select"  tabindex="3" required>
                                    {{-- <option value=""></option> --}}
                                    {{-- @foreach($departments as $department) --}}
                                    <option value="{{$employee_company->EmployeeDepartment[0]->id}}" >{{$employee_company->EmployeeDepartment[0]->name}}</option>
                                    {{-- @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-lg-3'>
                                <div class="form-group" id="data_5">
                                    <label class="font-normal">Projected Disbursement Date  :</label> 
                                    {{-- <label class="font-normal">Range select</label> --}}
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="form-control-sm form-control" value='{{date("m-d-Y")}}' name="date_from_projected" required/>
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="form-control-sm form-control"  value='{{date("m-d-Y", strtotime("+1 week"))}}'  name="date_to_projected" required/>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-3    '>
                                <div class="form-group" id="data_5">
                                    <label class="font-normal">Expected Delivery Date :</label> 
                                    {{-- <label class="font-normal">Range select</label> --}}
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="form-control-sm form-control" value='{{date("m-d-Y")}}' name="expected_delivery_date_from" required/>
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="form-control-sm form-control"  value='{{date("m-d-Y", strtotime("+1 week"))}}'  name="expected_delivery_date_to" required/>
                                    </div>
                                </div>
                                
                            </div>
                            <div class='col-lg-2'>
                                <label class="font-normal">Conversion Rate Used :</label> 
                                {{-- <input class='form-control' name='conversion_rate_used'> --}}
                                <select class='chosen-select input-sm' name='conversion_rate_used' required>
                                    <option></option>
                                    @foreach($currencies as $currency)
                                    <option {{($currency->code == "PHP") ? "selected":"" }} value='{{$currency->code}}'>{{$currency->symbol_native.' '.$currency->code}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    
                    <hr>
                    <h3>Details</h3>
                    <hr>
                    <div class="row" id=''> 
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        
                                        {{-- <th width='15%'>Budget Code (IO)</th> --}}
                                        <th>Cost Center</th>
                                        <th>Request Type</th>
                                        <th>Qty</th>
                                        <th>Unit of measure</th>
                                        <th>Total Amount</th>
                                        <th>Remaining Balance</th>
                                        <th>Material</th>
                                        <th>Date needed / ROI</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class='form-all'>
                                    @foreach($sb_reject_request->details as $key => $rejected)
                                    <tr id='{{$key+1}}'>
                                        <td>
                                            <select class='form-control' name='cost_center[{{$key+1}}]'  required> 
                                                <option></option>
                                                @foreach($cost_centers as $cost_center)
                                                <option value='{{$cost_center->dept_name}}' {{($rejected->cost_center == $cost_center->dept_name)?'SELECTED':''}}>{{$cost_center->dept_name}}</option>
                                           
                                                @endforeach
                                                <select>
                                                </td>
                                        <td>
                                             <select class='form-control' name='type_of_request[{{$key+1}}]' id='type_request{{$key+1}}' onchange='request_type({{$key+1}},this.value)'  required> 
                                                <option></option>
                                                <option value='Direct Expense' {{($rejected->type_of_request == "Direct Expense") ? "selected":"" }}  >Direct Expense</option>
                                                <option value='Inventoriable' {{($rejected->type_of_request == "Inventoriable") ? "selected":"" }} >Inventoriable</option>
                                                <option value='Asset' {{($rejected->type_of_request == "Asset") ? "selected":"" }} >Asset</option>
                                              
                                            <select>
                                        </td>
                                      
                                    <td><input class="form-control" type='number' type="text" min='.01' step='0.01' value="{{$rejected->qty}}"  id='qty{{$key+1}}' class='form-control' name='qty[{{$key+1}}]' required></td>
                                                <td>
                                                    <select class='form-control' name='unit_of_measure[{{$key+1}}]' id='unit_of_measure{{$key+1}}' required> 
                                                            <option></option>
                                                            @foreach($unit_of_measures as $unit_of_measure)  
                                                                <option value='{{$unit_of_measure->name}}' {{($rejected->unit_of_measure == $unit_of_measure->name)?'SELECTED':''}}>{{$unit_of_measure->name}}</option>
                                                            @endforeach
                                                        <select>
                                                        </td>
                                                        <td>
                                                        @if($rejected->no_vat =="Yes")
                                                            <b><i>Vat Inclusive</i><b><input id='vat_inclusive{{$key+1}}' onchange='do_vat_inclusive({{$key+1}},this.value)' value='@if($rejected->qty != null){{round(($rejected->unit_price * $rejected->qty),2)}}@else{{round(($rejected->unit_price),2)}}@endif' class='form-control' type='number' value='' step="0.01" min='.01'  required>
                                                            <b><i>Vat Exclusive</i><b><input id='vat_exclusive{{$key+1}}' onchange='do_vat_exclusive({{$key+1}},this.value)' class='form-control' type='number'  value='@if($rejected->qty != null){{round(($rejected->unit_price * $rejected->qty),2)}}@else{{round(($rejected->unit_price),2)}}@endif' step="0.01" name='unit_price[{{$key+1}}]' min='.01' readonly required>
                                                            <br><input type='checkbox' name='no_vat[{{$key+1}}]'  id='no_vat{{$key+1}}' onclick='calculate_no_vat({{$key+1}})' checked><label for='no_vat{{$key+1}}'><i><h5>Select if Non VAT Vendor</h5></i></label> 
                                                        @else
                                                            <b><i>Vat Inclusive</i><b><input id='vat_inclusive{{$key+1}}' onchange='do_vat_inclusive({{$key+1}},this.value)' value='@if($rejected->qty != null){{round((($rejected->unit_price * $rejected->qty)*1.12),2)}}@else{{round((($rejected->unit_price)*1.12),2)}}@endif' class='form-control' type='number' value='' step="0.01" min='.01' required>
                                                            <b><i>Vat Exclusive</i><b><input id='vat_exclusive{{$key+1}}' onchange='do_vat_exclusive({{$key+1}},this.value)' class='form-control' type='number'  value='@if($rejected->qty != null){{round(($rejected->unit_price * $rejected->qty),2)}}@else{{round(($rejected->unit_price),2)}}@endif' step="0.01" name='unit_price[{{$key+1}}]' min='.01' readonly required>
                                                            <br><input type='checkbox' name='no_vat[{{$key+1}}]'  id='no_vat{{$key+1}}' onclick='calculate_no_vat({{$key+1}})' ><label for='no_vat{{$key+1}}'><i><h5>Select if Non VAT Vendor</h5></i></label> 
                                                        @endif   </td>
                                                        <td><input id='remaining_balanace{{$key+1}}' class='form-control' type='number' value='{{$rejected->remaining_balance}}' step="0.01" name='remaining_balance[{{$key+1}}]' min='.00'  value='0.00'  required></td>
                                                        <td>
                                                            {{-- <input class='form-control' onchange='get_material_description(this.value,1)'  id='material_code1' name='material_code[1]'  placeholder='Material Code' readonly >
                                                             <br> --}}
                                                             @if($rejected->type_of_request == "Inventoriable")

                                                             <textarea  minlength='10' maxlength='40'  class='form-control' id='material_description{{$key+1}}' name='material_description[{{$key+1}}]' placeholder='Material Description'  required>{{$rejected->material_description}}</textarea></td>
                                                            @else
                                                            <textarea  minlength='10' maxlength='40'  class='form-control' id='material_description{{$key+1}}' name='material_description[{{$key+1}}]' placeholder='Material Description' disabled required>{{$rejected->material_description}}</textarea></td>
                                                            
                                                            @endif
                                                       
                                                             <td><input type='month' class='form-control' min='{{date('Y-m')}}' value='{{$rejected->date_needed}}' name='date_needed[{{$key+1}}]' required><br>
                                                                
                                                                ROI : @if($rejected->roi != null)<a href='{{url($rejected->roi)}}'>File</a> <input class='form-control' value='{{$rejected->roi}}' name='roi_old[{{$key+1}}]' type='hidden'>@endif
                                                                <input type='file' id='roi1' class='form-control' name='rio[{{$key+1}}]' ></td>
                                                            <td style='width:20%;'><textarea  class='form-control' name='remarks[{{$key+1}}]' placeholder='Remarks' >{{$rejected->remarks}}</textarea> <br>
                                                            <textarea  class='form-control' name='io_description[{{$key+1}}]' placeholder='Description' min='10' max='' id='io_description{{$key+1}}' required>{{$rejected->io_description}}</textarea>
                                                        </td>
                                                    </tr>
                                            @endforeach
                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan='9'><a onclick='add_row()' class="btn btn-sm btn-primary"> Add Row </a></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <b class='text-danger'><i>Supporting Documents (just click to download):</i></b><br>
                                       
                                        @if(count($rejected->sb_request->attachments))
                                            @foreach($rejected->sb_request->attachments as $attachment)
                                               <div id='attachment{{$attachment->id}}' > <a href='{{url($attachment->file_url)}}' target='_blank'> {{$attachment->file_name}}</a> <a onclick='remove_attachment({{$attachment->id}})' href='#' title='remove' class='text-danger'>X</a> <input value='{{$attachment->id}}' name='old_attachment[]' type='hidden' id='attachment{{$attachment->id}}'><br></div>
                                            @endforeach
                                        @else
                                        <i>No Supporting Documents</i>
                                        @endif
                                        <div class='row'>
                                            <div class='col-lg-12'>
                                                <label class="font-normal text-danger"><b><i> Please upload all supporting documents  </i></b></label> 
                                                <input class='form-control'  type='file' name='attachments[]' multiple required>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="form-group text-right mt-5">
                                        <div>
                                        @if(($employee_supervisor == null) || ($employee_bu_head == null) || ($company_info->cluster_head == null))
                                            <span  style='color:red;'>  Note : Please contact HR</span><br>
                                            <span  style='color:red;'> <i> You cannot submit this request</i></span><br>
                                            @else
                                            <button class="btn btn-primary " type="submit" id='submit'><i class="fa fa-check"></i>&nbsp;Submit</button>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="footer">
                    </div>
                    <script type='text/javascript'>
                        function add_row()
                        {
                            var id_form = $('.form-all').children().last().attr('id');
                            var id_form = parseInt(id_form) + 1;
                            
                            var  table_append = "<tr id="+id_form+">";
                                // table_append += "<td> <select class='form-control' name='budgted["+id_form+"]'  onchange='do_budget("+id_form+",this.value)' required><option></option> <option value='Budgted'>Existing IO</option> <option value='Not Budgeted'>New IO</option> </select>  <br> <div class='form-"+id_form+"'></div></td>";
                                    table_append += "<td><a  onclick='remove("+id_form+")' href='#' title='remove' class='text-danger'>X</a><br> <select class='form-control' name='cost_center["+id_form+"]'  required><option></option>@foreach($cost_centers as $cost_center) <option value='{{$cost_center->dept_name}}'>{{$cost_center->dept_name}}</option>@endforeach<select></td>";
                               
                                    table_append += "<td><select class='form-control' name='type_of_request["+id_form+"]' onchange='request_type("+id_form+",this.value)' id='type_request"+id_form+"'  required><option></option><option value='Direct Expense'>Direct Expense</option><option value='Inventoriable'>Inventoriable</option><option value='Asset'>Asset</option>  <select></td>";
                                    table_append += "<td><input  type='number' min='.01' step='0.01'  id='qty"+id_form+"' value='1'  class='form-control' name='qty["+id_form+"]' required></td>";
                                    table_append += "<td> <select class='form-control' name='unit_of_measure["+id_form+"]' id='unit_of_measure"+id_form+"'  required>  <option></option>@foreach($unit_of_measures as $unit_of_measure)<option value='{{$unit_of_measure->name}}'>{{$unit_of_measure->name}}</option> @endforeach </select> </td>";
                                    table_append += "<td><b><i>Vat Inclusive</i><b><input id='vat_inclusive"+id_form+"' onchange='do_vat_inclusive("+id_form+",this.value)' class='form-control' type='number'  step='0.01'  required><b><i>Vat Exclusive</i><b><input id='vat_exclusive"+id_form+"' onchange='do_vat_exclusive("+id_form+",this.value)' class='form-control' type='number'  step='0.01' name='unit_price["+id_form+"]' min='.01' readonly required><br><input type='checkbox' name='no_vat["+id_form+"]'   id='no_vat"+id_form+"'  onclick='calculate_no_vat("+id_form+")'><label for='no_vat"+id_form+"'><i><h5>Select if Non VAT Vendor</h5></i></label</td>";
                                
                                    table_append += "<td><input id='remaining_balance"+id_form+"' class='form-control' type='number'  step='0.01' name='remaining_balance["+id_form+"]' min='.00'  value='0.00'  required></td>";
                                    // table_append += "<td><input id='material_code"+id_form+"' onchange='get_material_description(this.value,"+id_form+")'  class='form-control'  placeholder='Material Code'  name='material_code["+id_form+"]' minlength='8' maxlength='18' readonly>";
                                    table_append += "<td><textarea id='material_description"+id_form+"'  minlength='10' maxlength='40'  class='form-control' placeholder='Material Description'  name='material_description["+id_form+"]' required></textarea></td>";
                                    table_append += "<td><input type='month' class='form-control' min='{{date('Y-m')}}' name='date_needed["+id_form+"]' required><br> ROI :<input type='file' id='roi"+id_form+"' class='form-control' name='rio["+id_form+"]' required></td>";
                                    table_append += "<td><textarea class='form-control' name='remarks["+id_form+"]' placeholder='Remarks'></textarea><br><textarea  class='form-control' name='io_description["+id_form+"]' placeholder='IO Description' id='io_description"+id_form+"' required></textarea></td>";
                                    
                                    table_append += "</tr>";
                                    
                                    $(".form-all").append(table_append); 
                                    
                                    $('<link/>', {
                                        rel: 'stylesheet',
                                        type: 'text/css',
                                        href: '{{ asset('bootstrap/css/plugins/chosen/bootstrap-chosen.css') }}'
                                    }).appendTo('head');
                                    var chosen_js = '{{ asset('bootstrap/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') }}';        
                                    $.getScript(chosen_js,function(jd) {
                                        $('.chosen-select').chosen({width: "100%"});
                                    });  
                                    $(".touchspin1").TouchSpin({
                                        buttondown_class: 'btn btn-white',
                                        buttonup_class: 'btn btn-white',
                                        min: 1,
                                    });
                                    $(".touchspin2").TouchSpin({
                                        min: 0,
                                        max: 100000000000000000000000,
                                        step: 0.01,
                                        decimals: 2,
                                        buttondown_class: 'btn btn-white',
                                        buttonup_class: 'btn btn-white'
                                    });
                                }
                                function remove_attachment(id)
                                {
                                    $("#attachment"+id).remove();
                                }
                                function request_type(id,value)
                                {
                                    // alert(id);
                                    if(value === "Inventoriable")
                                    {
                                        document.getElementById('material_description'+id).disabled = false;
                                        // document.getElementById('material_description'+id).readonly = false;
                                        document.getElementById("roi"+id).required = false;
                                    }
                                    else if(value === "Direct Expense")
                                    {
                                        document.getElementById('material_description'+id).disabled = true;
                                        document.getElementById("material_description"+id).required = false;
                                        document.getElementById("roi"+id).required = false;                                            
                                    }
                                    else 
                                    {
                                        document.getElementById('material_description'+id).disabled = true;
                                        document.getElementById("material_description"+id).required = false;
                                        
                                        document.getElementById("roi"+id).required = true;  
                                        
                                    }
                                }
                                function remove(id)
                                {
                                    $("#"+id).remove();
                                }
                                function do_vat_inclusive(id,value)
                                {
                                    if(document.getElementById('no_vat'+id).checked)
                                    {
                                        var total = value/1.12;
                                        if(value == "")
                                        {
                                            document.getElementById('vat_exclusive'+id).readOnly = false;
                                            document.getElementById("vat_exclusive"+id).value = "";
                                        }
                                        else
                                        {
                                            document.getElementById('vat_exclusive'+id).readOnly = true;
                                            document.getElementById("vat_exclusive"+id).value = value;
                                        }
                                    }
                                    else
                                    {
                                        var total = value/1.12;
                                        if(value == "")
                                        {
                                            document.getElementById('vat_exclusive'+id).readOnly = false;
                                            document.getElementById("vat_exclusive"+id).value = "";
                                        }
                                        else
                                        {
                                            document.getElementById('vat_exclusive'+id).readOnly = true;
                                            document.getElementById("vat_exclusive"+id).value = total.toFixed(2);
                                        }
                                    }
                                }
                                function do_vat_exclusive(id,value)
                                {
                                    let total = value*1.12;
                                    if(value == "")
                                    {
                                        document.getElementById('vat_inclusive'+id).readOnly = false;
                                        document.getElementById("vat_inclusive"+id).value = "";
                                    }
                                    else
                                    {
                                        document.getElementById('vat_inclusive'+id).readOnly = true;
                                        document.getElementById("vat_inclusive"+id).value =total.toFixed(2);
                                    }
                                }
                                function calculate_no_vat(id)
                                {
                                    if(document.getElementById("vat_exclusive"+id).value != "")
                                    {
                                        if(document.getElementById('no_vat'+id).checked)
                                        {
                                            total = document.getElementById('vat_inclusive'+id).value ;
                                            document.getElementById("vat_exclusive"+id).value = total;
                                        }
                                        else
                                        {
                                            total = document.getElementById('vat_inclusive'+id).value ;
                                            
                                            var total = total/1.12;
                                            document.getElementById("vat_exclusive"+id).value =  total.toFixed(2);
                                        }
                                    }
                                }
                        </script>
                    @endsection
                                    
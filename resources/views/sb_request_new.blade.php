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
                <label class="font-normal">Immediate Supervisor <span style='color:red;'>*</span> :  @if($employee_supervisor) {{$employee_supervisor->employee_head_info->first_name." ".$employee_supervisor->employee_head_info->last_name}} @else <i> Note : Please contact HR to edit your supervisor </i> @endif </label><br>
                <label class="font-normal">BU Head <span style='color:red;'>*</span> : @if($employee_bu_head) {{$employee_bu_head->employee_head_info->first_name." ".$employee_bu_head->employee_head_info->last_name}} @else <i> Note : Please contact HR to edit your BU Head </i> @endif </label><br>
                <label class="font-normal">Cluster Head <span style='color:red;'>*</span> : 
                      @if($company_info->id == 17)
                    <span style='color:red;'>Note: System will automatically determine the final cluster head based on inputted details .</span>
                        @foreach($cluster_heads as $clust)
                            <br>
                            {{$clust->cluster_head_info->name}}
                        @endforeach
                    @else
                    @if($company_info->cluster_head) {{$company_info->cluster_head->user_info->name}} @else <i> Note : Please place clusterhead at masterlist. </i> @endif 
                    @endif
                </label><br>
                {{-- {{dd($r)}} --}}
                <form  method='POST' id='myform' action='save-sb-request' onsubmit='return validation();show();' enctype="multipart/form-data"  >
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
                                                @foreach($employees as $employee)
                                                    <option  value="{{$employee->employee_info->user_id}}">{{$employee->employee_info->first_name}} {{$employee->employee_info->last_name}} - ({{$employee->employee_info->position}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endif
                        @endif
                    @endif
                @endif
                @if($r == "b")
                {{-- {{dd($r)}} --}}
                <div class="form-group">
                    <div class='row'>
                        <div class='col-lg-6'>
                            <label class="font-normal">Additional Approver <span style='color:red;'>*</span></label>
                            <select data-placeholder="Choose Approver" name='additional_approvers[]' class="chosen-select "  tabindex="1" multiple required>
                                @foreach($employees as $employee)
                                    @if($employee_supervisor->employee_head_info->user_id != $employee->employee_info->user_id)
                                        @if($employee_bu_head->employee_head_info->user_id != $employee->employee_info->user_id)
                                            @if($company_info->cluster_head->user_id != $employee->employee_info->user_id)
                                                @if(in_array($employee->employee_info->user_id,$cluster_heads_id))
                                                    @else
                                                        <option  value="{{$employee->employee_info->user_id}}">{{$employee->employee_info->first_name}} {{$employee->employee_info->last_name}} - {{$employee->position}} </option>
                                                    @endif
                                                @endif
                                        @endif    
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif
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
                                    <input type="text" class="form-control-sm form-control "  value='{{date("m-d-Y")}}' minDate='{{date("m-d-Y")}}' name="date_from_projected" required/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control-sm form-control"  value='{{date("m-d-Y", strtotime("+1 week"))}}' min='{{date("m-d-Y")}}'  name="date_to_projected" required/>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg-3    '>
                            <div class="form-group" id="data_5">
                                <label class="font-normal">Expected Delivery Date :</label> 
                                {{-- <label class="font-normal">Range select</label> --}}
                                <div class="input-daterange input-group" id="datepicker">
                                    <input type="text" class="form-control-sm form-control" value='{{date("m-d-Y")}}' min='{{date("m-d-Y")}}' name="expected_delivery_date_from" required/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control-sm form-control"  value='{{date("m-d-Y", strtotime("+1 week"))}}'  min='{{date("m-d-Y")}}' name="expected_delivery_date_to" required/>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg-2'>
                            <label class="font-normal">Conversion Rate Used :</label> 
                            {{-- <input class='form-control' name='conversion_rate_used'> --}}
                            <select class='chosen-select input-sm' name='conversion_rate_used' required>
                                <option></option>
                                @foreach($currencies as $currency)
                                    <option {{($currency->code == "PHP") ? "selected":"" }} value='{{$currency->code}}'>{{$currency->code}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input id='finance_head' type='hidden' name='finance_head' required>
                        <input id='cluster_head' type='hidden' name='cluster_head' required>
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
                                            <th width='15%'>Budget Code (IO)</th>
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
                                        <tr id='1'>
                                                <td>
                                                    <select class='form-control' name='budgted[1]'   onchange='do_budget(1,this.value)' required>
                                                        <option></option>
                                                        <option value='Budgted'>Existing IO</option>
                                                        <option value='Not Budgeted'>New IO</option>
                                                    </select>
                                                    <br>
                                                    <div class='form-1'>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class='form-control' name='type_of_request[1]' id='type_request1'  onchange='request_type(1,this.value)'  required> 
                                                    <option></option>
                                                    <option value='Direct Expense'>Direct Expense</option>
                                                    <option value='Inventoriable'>Inventoriable</option>
                                                    <option value='Asset'>Asset</option>
                                                    <select>
                                                </td>
                                                <td>
                                                    <input class="form-control" type='number' type="text"  value="1" id='qty1' min='.01' step="0.01" class='form-control' name='qty[1]' required></td>
                                                <td>
                                                    <select class='form-control' name='unit_of_measure[1]' id='unit_of_measure1' required> 
                                                    <option></option>
                                                    @foreach($unit_of_measures as $unit_of_measure)  
                                                    <option value='{{$unit_of_measure->name}}'>{{$unit_of_measure->name}}</option>
                                                    @endforeach
                                                    <select>
                                                </td>
                                                <td>
                                                     
                                                    <b><i>VAT Inclusive</i><b><input id='vat_inclusive1' onchange='do_vat_inclusive(1,this.value)' class='form-control' type='number'  step="0.01" name='vat_inclusive[1]' min='.01' required>
                                                    <b><i>VAT Exclusive</i><b><input id='vat_exclusive1' onchange='do_vat_exclusive(1,this.value)' class='form-control' type='number'  step="0.01" name='unit_price[1]' min='.01' readonly required>
                                                    <input name='plant_vat1' id='plant_vat1' value='' onchange='do_plant_vat(1,this.value)' type='hidden'>
                                                    <br><input type='checkbox' name='no_vat[1]'  id='no_vat1' onclick='calculate_no_vat(1)' ><label for='no_vat1'><i><h5>Select if Non VAT Vendor</h5></i></label>

                                                </td>
                                                <td><input id='remaining_balanace1' class='form-control' type='number'  step="0.01" name='remaining_balance[1]' min='.00'  value='0.00'  readonly></td>
                                                <td><input class='form-control' onchange='get_material_description(this.value,1)'  id='material_code1' name='material_code[1]'  placeholder='Material Code' readonly > <br><textarea  minlength='10' maxlength='40'  class='form-control' id='material_description1' name='material_description[1]' placeholder='Material Description' readonly></textarea></td>
                                                <td><input type='month' class='form-control' min='{{date('Y-m')}}' id='need_month1' name='date_needed[1]' required><br> ROI :<input type='file' id='roi1' class='form-control' name='rio[1]' required></td>
                                                <td style='width:20%;'><textarea  class='form-control' name='remarks[1]' placeholder='Remarks' ></textarea> <br>
                                                    <textarea  class='form-control' name='io_description[1]' placeholder='IO Description' id='io_description1' required></textarea>
                                                </td>
                                        </tr>
                                    </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan='9'>    <a onclick='add_row()'  class="btn btn-sm btn-primary" > Add Row </a></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                    <hr>
                                    <div class="form-group">
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
                        var list = [];
                        function add_row()
                        {
                            var id_form = $('.form-all').children().last().attr('id');
                            var id_form = parseInt(id_form) + 1;
                            
                            var  table_append = "<tr id="+id_form+">";
                            table_append += "<td> <a  onclick='remove("+id_form+")' href='#' title='remove' class='text-danger'>X</a><br> <select class='form-control' name='budgted["+id_form+"]'  onchange='do_budget("+id_form+",this.value)' required><option></option> <option value='Budgted'>Existing IO</option> <option value='Not Budgeted'>New IO</option> </select>  <br> <div class='form-"+id_form+"'></div></td>";
                            
                            table_append += "<td><select class='form-control' name='type_of_request["+id_form+"]' onchange='request_type("+id_form+",this.value)' id='type_request"+id_form+"'  required><option></option><option value='Direct Expense'>Direct Expense</option><option value='Inventoriable'>Inventoriable</option><option value='Asset'>Asset</option>  <select></td>";
                            table_append += "<td><input  type='number' min='.01' step='0.01'  id='qty"+id_form+"' value='1'  class='form-control' name='qty["+id_form+"]' required></td>";
                            table_append += "<td> <select class='form-control' name='unit_of_measure["+id_form+"]' id='unit_of_measure"+id_form+"'  required>  <option></option>@foreach($unit_of_measures as $unit_of_measure)<option value='{{$unit_of_measure->name}}'>{{$unit_of_measure->name}}</option> @endforeach </select> </td>";
                            table_append += "<td><b><i>Vat Inclusive</i><b><input id='vat_inclusive"+id_form+"' name='vat_inclusive["+id_form+"]' onchange='do_vat_inclusive("+id_form+",this.value)' class='form-control' type='number'  step='0.01'  required><b><i>Vat Exclusive</i><b><input id='vat_exclusive"+id_form+"' onchange='do_vat_exclusive("+id_form+",this.value)' class='form-control' type='number'  step='0.01' name='unit_price["+id_form+"]' min='.01' readonly required><br><input type='checkbox' name='no_vat["+id_form+"]'   id='no_vat"+id_form+"'  onclick='calculate_no_vat("+id_form+")'><label for='no_vat"+id_form+"'><i><h5>Select if Non VAT Vendor</h5></i></label>";
                            table_append += "<input name='plant_vat"+id_form+"' id='plant_vat"+id_form+"' value='' onchange='do_plant_vat("+id_form+",this.value)' type='hidden'></td>";
                            table_append += "<td><input id='remaining_balanace"+id_form+"' class='form-control' type='number'  step='0.01' name='remaining_balance["+id_form+"]' min='.00'  value='0.00'  readonly></td>";
                            table_append += "<td><input id='material_code"+id_form+"' onchange='get_material_description(this.value,"+id_form+")'  class='form-control'  placeholder='Material Code'  name='material_code["+id_form+"]' minlength='8' maxlength='18' readonly>";
                            table_append += "<br><textarea id='material_description"+id_form+"'  minlength='10' maxlength='40'  class='form-control' placeholder='Material Description'  name='material_description["+id_form+"]' readonly></textarea></td>";
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
                        function do_budget(id,value)
                        {
                            $("#budget-form-"+id).remove();
                            $('.budget-form-'+id).children().remove();
                            if(value === "Budgted")
                            {
                                var new_form = "<input placeholder='Budget Code(IO)' onchange='get_io_info(this.value,"+id+")' id='budget-form-"+id+"' class='form-control' type='text' minlength='12' maxlength='12' name='budget_line["+id+"]' required>";
                                $(".form-"+id).append(new_form); 
                                // alert('renz');
                                document.getElementById('remaining_balanace'+id).removeAttribute('readonly');
                                document.getElementById("remaining_balanace"+id).required = true;
                                document.getElementById("remaining_balanace"+id).value = "0";
                                
                                document.getElementById("type_request"+id).disabled = true;
                                document.getElementById("type_request"+id).required = false;
                                document.getElementById("type_request"+id).value = "";
                                
                                document.getElementById("unit_of_measure"+id).disabled = true;
                                document.getElementById("unit_of_measure"+id).required = false;
                                document.getElementById("unit_of_measure"+id).value = "";
                                
                                document.getElementById("material_code"+id).disabled = true;
                                document.getElementById("material_description"+id).disabled = true;
                                document.getElementById("material_code"+id).value = "";
                                
                                document.getElementById("material_description"+id).value = "";
                                document.getElementById("io_description"+id).disabled = true;
                                document.getElementById("io_description"+id).readonly = "";
                            }
                            else if(value === "Not Budgeted")
                            {
                                var company_id = $("#company_id :selected").val(); 
                                if(company_id == null)
                                {
                                    swal({
                                        title: "Error Message",
                                        text: "Please select company first"
                                    });
                                    document.getElementById("submit").disabled = true;
                                    document.getElementById("myDiv").style.display="none";
                                    return false;
                                }
                            document.getElementById("myDiv").style.display="block";
                            $.ajax({    //create an ajax request to load_page.php
                                
                                type: "GET",
                                url: "{{ url('/get-cost-center/') }}",            
                                data: {
                                    "company_id" : company_id,
                                }     ,
                                dataType: "json",   //expect html to be returned
                                success: function(data){  

                                    var new_form = "<div class='budget-form-"+id+"'><select id='budget-form-"+id+"' data-placeholder'Choose Costcenter'  name='cost_center["+id+"]' class='chosen-select'   required='true tabindex='1' required>";
                                        new_form += "<option value=''>Cost Center</option>";
                                        jQuery.each(data, function(id) {
                                            new_form += "<option value="+data[id].COSTCENTER+">"+data[id].COSTCENTER+" - "+data[id].COCNTR_TXT+"</option>";
                                        });
                                        
                                        new_form += "</select> <br> <br>";
                                        new_form += "<select  data-placeholder'Choose Plant'  name='plant["+id+"]' class='chosen-select' onchange='plant_info("+id+",this.value)'   required='true tabindex='1' required> <option value=''>Plant</option> @foreach($company_info->plant_info as $plant_info) <option value='{{$plant_info->plant}}-{{$plant_info->approver_id}}-{{$plant_info->no_vat}}-{{$plant_info->cluster_head}}'>{{$plant_info->plant}}-{{$plant_info->plant_name}}</option> @endforeach";
                                        new_form += "</select></div>";
                                    
                                    $(".form-"+id).append(new_form); 
                                        document.getElementById('remaining_balanace'+id).readOnly = true;
                                        document.getElementById("remaining_balanace"+id).required = false;
                                        document.getElementById("remaining_balanace"+id).value = "0";
                                        document.getElementById("type_request"+id).value = "";
                                        document.getElementById("type_request"+id).disabled = false;
                                        document.getElementById("type_request"+id).required = true;
                                        document.getElementById("unit_of_measure"+id).readonly = false;
                                        document.getElementById("unit_of_measure"+id).required = true;
                                        document.getElementById("unit_of_measure"+id).value = "";
                                        document.getElementById("material_code"+id).disabled = false;
                                        document.getElementById("material_code"+id).value = "";
                                        document.getElementById("material_description"+id).disabled = false;
                                        document.getElementById("material_description"+id).value = "";
                                        document.getElementById("io_description"+id).disabled = false;
                                        document.getElementById("io_description"+id).value = "";
                                        document.getElementById("myDiv").style.display="none";
                                        $('<link/>', {
                                                rel: 'stylesheet',
                                                type: 'text/css',
                                                href: '{{ asset('bootstrap/css/plugins/chosen/bootstrap-chosen.css') }}'
                                            }).appendTo('head');
                                            var chosen_js = '{{ asset('bootstrap/js/plugins/chosen/chosen.jquery.js') }}';        
                                            $.getScript(chosen_js,function(jd) {
                                                $('.chosen-select').chosen({width: "100%"});
                                            });    
                                        },
                                error: function(e)
                                {
                                    swal({
                                        title: "Error Message",
                                        text: "Please contact your system administrator"
                                    });
                                }
                            });
                                
                                
                            }
                        }
                        function request_type(id,value)
                        {
                            // alert(id);
                            if(value === "Inventoriable")
                            {
                                
                                document.getElementById("material_code"+id).removeAttribute('readonly');
                                document.getElementById('material_code'+id).disabled = false;
                                document.getElementById("material_code"+id).required = false;
                                document.getElementById("material_code"+id).readonly = false;
                                // document.getElementById('material_description'+id).removeAttribute('readonly');
                                document.getElementById("material_description"+id).required = false;
                                document.getElementById("unit_of_measure"+id).value = "";
                                document.getElementById("unit_of_measure"+id).disabled = false;
                                document.getElementById("qty"+id).value = "1";
                                document.getElementById("qty"+id).disabled = false;
                                document.getElementById("material_code"+id).value = "";
                                document.getElementById("material_description"+id).value = "";
                                document.getElementById("roi"+id).required = false;
                            }
                            else if(value === "Direct Expense")
                            {
                                document.getElementById('material_code'+id).disabled = true;
                                document.getElementById("material_code"+id).required = false;
                                document.getElementById('material_description'+id).disabled = true;
                                document.getElementById("material_description"+id).required = false;
                                document.getElementById("unit_of_measure"+id).value = "";
                                document.getElementById("unit_of_measure"+id).disabled = false;
                                document.getElementById("unit_of_measure"+id).required = false;
                                document.getElementById("qty"+id).value = "";
                                document.getElementById("qty"+id).disabled = false;
                                document.getElementById("qty"+id).required = false;
                                document.getElementById("material_code"+id).value = "";
                                document.getElementById("material_description"+id).value = "";
                                document.getElementById("roi"+id).required = false;                                            
                            }
                            else 
                            {
                                document.getElementById('material_code'+id).disabled = true;
                                document.getElementById("material_code"+id).required = false;
                                document.getElementById('material_description'+id).disabled = true;
                                document.getElementById("material_description"+id).required = false;
                                document.getElementById("unit_of_measure"+id).value = "";
                                document.getElementById("unit_of_measure"+id).disabled = false;
                                document.getElementById("qty"+id).value = "1";
                                document.getElementById("qty"+id).disabled = false;
                                document.getElementById("material_code"+id).value = "";
                                document.getElementById("material_description"+id).value = "";
                                document.getElementById("roi"+id).required = true;  
                                
                            }
                        }
                        function get_material_description(code,id)
                        {
                            // alert(id);
                            document.getElementById("myDiv").style.display="block";
                            //   document.getElementById("myDiv").style.display="none";
                            var company_id = $("#company_id :selected").val(); 
                            if(company_id == null)
                            {
                                swal({
                                    title: "Error Message",
                                    text: "Please select company first"
                                });
                                document.getElementById("submit").disabled = true;
                                document.getElementById("myDiv").style.display="none";
                                return false;
                            }
                            $.ajax({    
                                
                                type: "GET",
                                url: "{{ url('/get-material-info') }}",            
                                data: {
                                    "io" : code,
                                    "company_id" : company_id,
                                }     ,
                                dataType: "json",   //expect html to be returned
                                success: function(data){  
                                    
                                    document.getElementById("myDiv").style.display="none";
                                    if(data['O_BASEUNIT'] == "")
                                    {
                                        swal({
                                            title: "Error Message",
                                            text: "Please check your material code again"
                                        });
                                        document.getElementById("submit").disabled = true;
                                        document.getElementById("myDiv").style.display="none";
                                        return false;
                                    }
                                    else
                                    {
                                        document.getElementById("submit").readonly = false;
                                        document.getElementById("material_description"+id).value = data['O_MATDESC'];
                                        $("#unit_of_measure"+id).append(new Option(data['O_BASEUNIT'],data['O_BASEUNIT']));
                                        document.getElementById("unit_of_measure"+id).value = data['O_BASEUNIT'];
                                        document.getElementById("submit").disabled = false;
                                    }
                                },
                                error: function(e)
                                {
                                    swal({
                                        title: "Error Message",
                                        text: e
                                    });
                                }
                            });
                            
                        }
                        function get_io_info(io,id)
                        {
                            
                            var company_id = $("#company_id :selected").val(); 
                            if(company_id == null)
                            {
                                swal({
                                    title: "Error Message",
                                    text: "Please select company first"
                                });
                                document.getElementById("submit").disabled = true;
                                document.getElementById("myDiv").style.display="none";
                                return false;
                            }
                            document.getElementById("myDiv").style.display="block";
                            $.ajax({    //create an ajax request to load_page.php
                                
                                type: "GET",
                                url: "{{ url('/get-budget-info/') }}",            
                                data: {
                                    "io" : io,
                                    "company_id" : company_id,
                                }     ,
                                dataType: "json",   //expect html to be returned
                                success: function(data){  
                                    var  id_value = id;
                                    console.log(data);
                                    var index = list.map(x => {
                                    return x.id_value;
                                    }).indexOf(id_value);
                                    // alert(index);
                                    var value = data[6];
                                    document.getElementById("plant_vat"+id).value = data[7];
                                    document.getElementById('vat_inclusive'+id).readOnly = false;
                                    if(index == -1)
                                    {
                                        list.push({id_value,value});
                                        document.getElementById("finance_head").value = value;
                                    }
                                    else
                                    {
                                        list.splice(index, 1);
                                        list.push({id_value,value});
                                        document.getElementById("finance_head").value = value;
                                    }
                                    document.getElementById("cluster_head").value = data[8];
                                    console.log(list);
                                    if(data[4] != data[0]['O_COMPCODE'])
                                    {
                                        swal({
                                            title: "Error Message",
                                            text: "Please check again your company or budget code inputted"
                                        });
                                        document.getElementById("submit").disabled = true;
                                        document.getElementById("myDiv").style.display="none";
                                        return false;
                                    }
                                    if(data[0]['O_AUFNR'] == "")
                                    {
                                        swal({
                                            title: "Error Message",
                                            text: "Please check again your company or budget code inputted"
                                        });
                                        document.getElementById("submit").disabled = true;
                                        document.getElementById("myDiv").style.display="none";
                                        return false;
                                    }
                                    else
                                    {
                                        if(data[0]['O_MATERIAL'] != "")
                                        {
                                            document.getElementById("material_code"+id).value = parseInt(data[0]['O_MATERIAL']);
                                            
                                        }
                                        else
                                        {
                                            document.getElementById("material_code"+id).value = (data[0]['O_MATERIAL']);
                                        }
                                        document.getElementById("material_description"+id).value = data[1]['O_MATDESC'];
                                        document.getElementById("io_description"+id).value = data[0]['O_IODESC'];
                                        if((data[2] >= 1000) && (data[2] < 2000) && (data[2] != 1020))
                                        {
                                            $("#unit_of_measure"+id).append(new Option(data[1]['O_BASEUNIT'],data[1]['O_BASEUNIT']));
                                            document.getElementById("unit_of_measure"+id).value = data[1]['O_BASEUNIT'];
                                            document.getElementById("qty"+id).value = "1";
                                            document.getElementById("qty"+id).disabled = false;
                                            document.getElementById("qty"+id).required = true;
                                            document.getElementById("type_request"+id).value = "Inventoriable";
                                            document.getElementById("roi"+id).required = false;  
                                        }
                                        else if(data[2] == 1020)
                                        {
                                            
                                            document.getElementById("unit_of_measure"+id).value = data[1]['O_BASEUNIT'];
                                            document.getElementById("qty"+id).value = "1";
                                            document.getElementById("qty"+id).disabled = false;
                                            document.getElementById("unit_of_measure"+id).disabled = false;
                                            document.getElementById("type_request"+id).value = "Asset";
                                            document.getElementById("roi"+id).required = true;  
                                        }
                                        else
                                        {
                                                document.getElementById("unit_of_measure"+id).value = "";
                                                document.getElementById("qty"+id).value = "";
                                                document.getElementById("qty"+id).disabled = false;
                                                document.getElementById("qty"+id).required = false;
                                                document.getElementById("type_request"+id).value = "Direct Expense";
                                                document.getElementById("roi"+id).required = false;  
                                        }
                                        
                                        document.getElementById("submit").disabled = false;
                                        
                                        
                                    }
                                    if (data[5] == null)
                                    {
                                        let today = new Date(),
                                        day = today.getDate(),
                                        month = today.getMonth()+1, //January is 0
                                        year = today.getFullYear();
                                            if(day<10){
                                                    day='0'+day
                                                } 
                                            if(month<10){
                                                month='0'+month
                                            }
                                            today = year+'-'+month;

                                        // var x = document.getElementById("needmonth1").min= "{{date('Y-m')}}";
                                        document.getElementById("need_month1").setAttribute("min", today);
                                        // alert('renz');
                                    }
                                    else
                                    {
                                        let today = new Date(),
                                        day = today.getDate(),
                                        month = today.getMonth(), //January is 0
                                        year = today.getFullYear();
                                            if(day<10){
                                                    day='0'+day
                                                } 
                                            if(month<10){
                                                month='0'+month
                                            }
                                            today = year+'-'+month;

                                        // var x = document.getElementById("needmonth1").min= "{{date('Y-m')}}";
                                        document.getElementById("need_month1").setAttribute("min", today);
                                    }
                                    
                                    document.getElementById("myDiv").style.display="none";
                                },
                                error: function(e)
                                {
                                    swal({
                                        title: "Error Message",
                                        text: "Please contact your system administrator"
                                    });
                                }
                            });
                        }
                        function reset_value()
                        {
                            document.getElementById("myform").reset();
                        }
                        function remove(id)
                        {
                            $("#"+id).remove();
                            var index = list.map(x => {
                            return x.id;
                            }).indexOf(id);
                            list.splice(index, 1);
                                
                            console.log(list);
                        }
                        function plant_info(id_value,value)
                        {
                            // var id = id_value;
                            // alert(value);
                            var res = value.split("-");
                            value = res[1];
                            var value_plant = res[2];
                            document.getElementById("plant_vat"+id_value).value = value_plant;
                            var index = list.map(x => {
                            return x.id_value;
                            }).indexOf(id_value);
                            // alert(index);
                            if(index == -1)
                            {
                                list.push({id_value,value});
                                document.getElementById("finance_head").value = value;
                            }
                            else
                            {
                                list.splice(index, 1);
                                list.push({id_value,value});
                                document.getElementById("finance_head").value = value;
                            }
                            document.getElementById("cluster_head").value = res[3];
                            console.log(list);
                        }
                        function validation()
                        {
                            jQuery.each(list, function(id) {
                                            a = 0;
                                            var exact_value = list[0].value;
                                            if(list[id].value != exact_value)
                                            {
                                                a = 1;
                                            }
                                            ;
                                        });
                                    if(a == 1)
                                    {
                                        
                                    swal({
                                        title: "Error Message",
                                        text: "Please separate request for difference division!"
                                    });
                                        return false;
                                    }
                                    document.getElementById("myDiv").style.display="block";
                        }
                        function do_vat_inclusive(id,value)
                        {
                            var no_vat_plant = document.getElementById("plant_vat"+id).value;  
                            if(no_vat_plant == "")
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
                            else
                            {
                                document.getElementById('vat_exclusive'+id).readOnly = true;
                                    document.getElementById("vat_exclusive"+id).value = value;
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
                            // alert('renz');
                            var no_vat_plant = document.getElementById("plant_vat"+id).value;  
                            if(no_vat_plant == "")
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
                            else
                            {
                                total = document.getElementById('vat_inclusive'+id).value ;
                                document.getElementById("vat_exclusive"+id).value = total;
                            }
                        }
                        function do_plant_vat(id,value)
                        {
                            var val = document.getElementById('vat_inclusive'+id).value ;
                            if(val != "")
                            {
                            if(value == "")
                            {
                                    var val = document.getElementById('vat_inclusive'+id).value ;
                                    var total = val/1.12;
                                    document.getElementById("vat_exclusive"+id).value =  total.toFixed(2);
                            } 
                            else
                            {
                                    var val = document.getElementById('vat_inclusive'+id).value ;
                                    document.getElementById("vat_exclusive"+id).value =  val.toFixed(2);
                            }
                            }
                        }
                    </script>
@endsection
                                
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
                Reference Number : {{$compa->company_info->company_abbreviation}}-{{date('Ym',strtotime($compa->created_at))}}-{{str_pad($compa->ref_id, 4, '0', STR_PAD_LEFT)}} <br>
                Reason :  {{$compa->reason}}  <br>
                Remarks :  {{$compa->remarks}}  <br>
                <hr>
                <label class="font-normal">Immediate Supervisor <span style='color:red;'>*</span> :  @if($employee_supervisor) {{$employee_supervisor->employee_head_info->first_name." ".$employee_supervisor->employee_head_info->last_name}} @else <i> Note : Please contact HR to edit your supervisor </i> @endif </label><br>
                <label class="font-normal">BU Head <span style='color:red;'>*</span> : @if($employee_bu_head) {{$employee_bu_head->employee_head_info->first_name." ".$employee_bu_head->employee_head_info->last_name}} @else <i> Note : Please contact HR to edit your BU Head </i> @endif </label><br>
                <label class="font-normal">Cluster Head <span style='color:red;'>*</span> :   @if($company_info->id == 17)
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
                <form  method='POST' id='myform' action='save-refile-request/{{$compa->id}}' onsubmit='return validation();show();' enctype="multipart/form-data"  >
                    {{ csrf_field() }}
                    <input id='finance_head' type='hidden' name='finance_head' value='{{$finance_approver->approver_id}}' required>
                    <input id='cluster_head' type='hidden' name='cluster_head' value='{{$cluster_head->approver_id}}' required>
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-lg-3'>
                                <label class="font-normal">Company <span style='color:red;'>*</span></label>
                                <select data-placeholder="Choose Company" name='company' class="chosen-select "   id='company_id' required="true" tabindex="1" required>
                                    {{-- <option value=""></option> --}}
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
                    @if($r == "b")
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-lg-6'>
                                <label class="font-normal">Additional Approver <span style='color:red;'>*</span></label>
                                <select data-placeholder="Choose Approver" name='additional_approvers[]' class="chosen-select "  tabindex="1" multiple >
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
                    <hr>
                    <h3>Details</h3>
                    <hr>
                    <div class="row" id=''> 
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        
                                        <th width='15%'>Budget Code (IO)</th>
                                        <th>Details</th>
                                        <th>Qty</th>
                                        <th>Total Amount</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody class='form-all'>
                                    @foreach($compa->details as $key => $detail)
                                    <tr id='{{$key+1}}'>
                                        <td>
                                            <input placeholder='Budget Code(IO)' id='budget_code1' onchange='get_io_info(this.value,1)' id='budget-form-1' value='{{$detail->budget_code}}' class='form-control' type='text' minlength='12' maxlength='12' name='budget_line[1]' required>
                                        </td>
                                        <td>
                                            Controlling Area : <input class='form-control' id='controlling_area{{$key+1}}' name='controlling_area[{{$key+1}}]' value='{{$detail->controlling_area}}' readonly required><br>
                                            IO Description : <input class='form-control' id='io_description{{$key+1}}' name='io_description[{{$key+1}}]' readonly value='{{$detail->budget_description}}' required> </span><br>
                                            Unit of Measure :   <input class='form-control' id='unit_of_measure{{$key+1}}' name='unit_of_measure[{{$key+1}}]' value='{{$detail->unit_of_measure}}' readonly required><br>
                                        </td>
                                        <td><input class="form-control" type='number' type="text"min='.01' step='0.01' value="{{$detail->qty}}"  id='qty{{$key+1}}' class='form-control' name='qty[{{$key+1}}]'  ></td>
                                        <td>
                                            <input  class='form-control' type='number'  step="0.01" name='total_amount[{{$key+1}}]' min='.01'  value='{{$detail->amount}}'  required>
                                        </td>
                                        <td>
                                            <input type='month' class='form-control' id='date_from{{$key+1}}' min='{{date('Y-m')}}' onchange='change_from_month({{$key+1}},this.value)'  value='{{$detail->date_from}}' max="{{date('Y')}}-12" name='date_from[{{$key+1}}]'  required><br>
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label" >Version </label>
                                                <label class="col-sm-1 col-form-label">: </label>
                                                <div class="col-sm-6"> 
                                                    <input class='form-control'  name='version_from[{{$key+1}}]' value='{{$detail->version_from}}' type='number'  min='0' max='10'   required>
                                                  
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type='month' class='form-control' min='{{date('Y-m')}}' max="{{date('Y')}}-12"  onchange='change_to_month({{$key+1}},this.value)' value='{{$detail->date_to}}' name='date_to[{{$key+1}}]' id='date_to{{$key+1}}'  required> <br>
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label" >Version </label>
                                                <label class="col-sm-1 col-form-label">: </label>
                                                <div class="col-sm-6"> 
                                                    <select name='version_to[{{$key+1}}]' class='form-control' id='version_to{{$key+1}}' required>
                                                        <option value='{{$detail->version_to}}'>{{$detail->version_to}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td><textarea  class='form-control' name='reason[{{$key+1}}]' placeholder='Reason' required>{{$detail->remarks}}</textarea></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan='9'><a onclick='add_row()'  class="btn btn-sm btn-primary" > Add Row </a></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <b class='text-danger'><i>Supporting Documents (just click to download):</i></b><br>
                       
                        @if(count($compa->attachments))
                            @foreach($compa->attachments as $attachment)
                               <div id='attachment{{$attachment->id}}' > <a href='{{url($attachment->file_url)}}' target='_blank'> {{$attachment->file_name}}</a> <a onclick='remove_attachment({{$attachment->id}})' href='#' title='remove' class='text-danger'>X</a> <input value='{{$attachment->id}}' name='old_attachment[]' type='hidden' id='attachment{{$attachment->id}}'><br></div>
                            @endforeach
                        @else
                        <i>No Supporting Documents</i>
                        @endif
                        <div class='row'>
                            <div class='col-lg-12'>
                                <label class="font-normal text-danger"><b><i> Add additional supporting documents  </i></b></label> 
                                <input class='form-control' id='final_attachment' type='file' name='attachments[]' multiple required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right mt-5">
                        <div>
                            <button class="btn btn-primary " type="submit" id='submit'><i class="fa fa-check"></i>&nbsp;Submit</button>
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
                table_append += "<td><a  onclick='remove("+id_form+")' href='#' title='remove' class='text-danger'>X</a><br><input placeholder='Budget Code(IO)' id='budget_code"+id_form+"' onchange='get_io_info(this.value,"+id_form+")' id='budget-form-"+id_form+"' class='form-control' type='text' minlength='12' maxlength='12' name='budget_line["+id_form+"]' required> </td>";
                table_append += "<td> Controlling Area : <input class='form-control' id='controlling_area"+id_form+"' name='controlling_area["+id_form+"]' readonly required><br> IO Description : <input class='form-control' id='io_description"+id_form+"' name='io_description["+id_form+"]' readonly required><br>Unit of Measure: <input class='form-control' id='unit_of_measure"+id_form+"' name='unit_of_measure["+id_form+"]' readonly required><br></td>";
                table_append += "<td><input  type='number' min='.01' step='0.01'  id='qty"+id_form+"' value='1'  class='form-control' name='qty["+id_form+"]' required></td>";
                table_append += "<td> <input  class='form-control' type='number'  step='0.01' name='total_amount["+id_form+"]' min='.01'   required> </td>";
                table_append += "<td><input id='date_from"+id_form+"' onchange='change_from_month("+id_form+",this.value)' type='month' class='form-control' min='{{date('Y-m')}}' max='{{date('Y')}}-12' name='date_from["+id_form+"]' readonly  required> <br> <div class='form-group  row'><label class='col-sm-4 col-form-label'>Version </label>";
                table_append += "<label class='col-sm-1 col-form-label'>: </label><div class='col-sm-6'>  <input class='form-control'  name='version_from["+id_form+"]' type='number'  min='0' max='10'   required> </select></div></div></td>";
                table_append += "<td><input id='date_to"+id_form+"' onchange='change_to_month("+id_form+",this.value)'  type='month' class='form-control' min='{{date('Y-m')}}' max='{{date('Y')}}-12' name='date_to["+id_form+"]' readonly required><br> <div class='form-group  row'><label class='col-sm-4 col-form-label'>Version </label>";
                table_append += "<label class='col-sm-1 col-form-label'>: </label><div class='col-sm-6'> <select id='version_to"+id_form+"'  name='version_to["+id_form+"]' class='form-control'  required></select></div></div></td>";
                table_append += "<td><textarea  class='form-control' name='reason[]' placeholder='Reason' required></textarea></td></tr>";
                $(".form-all").append(table_append); 
            }
            function get_io_info(io,id)
            {
                // alert(id);
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
                        
                        console.log(data[0]['O_AUFNR']);
                        var  id_value = id;
                        console.log(data);
                        var index = list.map(x => {
                        return x.id_value;
                        }).indexOf(id_value);
                        // alert(index);
                        var value = data[6];
                        console.log(list);
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
                            // alert(id);
                            document.getElementById("controlling_area"+id).value = data[3];
                            document.getElementById("io_description"+id).value = data[0]['O_IODESC'];
                            document.getElementById("unit_of_measure"+id).value = data[1]['O_BASEUNIT'];
                            document.getElementById("submit").disabled = false;
                            $('#date_from'+id).removeAttr('readonly');
                            document.getElementById("date_from"+id).value  = "";
                            $('#date_to'+id).removeAttr('readonly');
                            document.getElementById("date_to"+id).value  = "";
                            if((data[2] >= 1000) && (data[2] < 2000) && (data[2] != 1020))
                            {
                                document.getElementById("qty"+id).value = "1";
                                document.getElementById("qty"+id).disabled = false;
                                document.getElementById("qty"+id).required = true;
                            }
                            else if(data[2] == 1020)

                            {
                                document.getElementById("qty"+id).required = true;
                                document.getElementById("qty"+id).value = "1";
                                document.getElementById("qty"+id).disabled = false;
                            }
                            else
                            {
                                    document.getElementById("qty"+id).value = "";
                                    document.getElementById("qty"+id).disabled = false;
                                    document.getElementById("qty"+id).required = false;
                            }
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
            // function change_from_month(id,value)
            // {
            //     document.getElementById("myDiv").style.display="block";
            //     var io=document.getElementById("budget_code"+id).value;
            //     var company_id = $("#company_id :selected").val(); 
            //     // alert(value);
            //     $.ajax({    //create an ajax request to load_page.php
                    
            //         type: "GET",
            //         url: "{{ url('/get-budget-info/') }}",            
            //         data: {
            //             "io" : io,
            //             "company_id" : company_id,
            //             "value" : value,
            //         }     ,
            //         dataType: "json",   //expect html to be returned
            //         success: function(data){  
            //             $('#version_from'+id).children().remove();
            //             var i;
            //             var last_value = data[0]['O_VERSN'] ;
            //             for(i = 0;i <= last_value;i++)
            //             {
            //                 $('#version_from'+id).append('<option value='+i+'>'+i+'</option>');
            //             }
                       
            //             document.getElementById("myDiv").style.display="none";
            //         },
            //         error: function(e)
            //         {
            //             swal({
            //                 title: "Error Message",
            //                 text: "Please contact your system administrator"
            //             });
            //         }
            //     });
                
            // }
            function remove_attachment(id)
            {
                $("#attachment"+id).remove();
            }
            function change_to_month(id,value)
            {
                document.getElementById("myDiv").style.display="block";
                var io=document.getElementById("budget_code"+id).value;
                var company_id = $("#company_id :selected").val(); 
                // alert(value);
                $.ajax({    //create an ajax request to load_page.php
                    
                    type: "GET",
                    url: "{{ url('/get-budget-info/') }}",            
                    data: {
                        "io" : io,
                        "company_id" : company_id,
                        "value" : value,
                    }     ,
                    dataType: "json",   //expect html to be returned
                    success: function(data){  
                        $('#version_to'+id).children().remove();
                        var second = parseInt(data[0]['O_VERSN'])-1
                        $('#version_to'+id).append('<option value='+data[0]['O_VERSN']+'>'+data[0]['O_VERSN']+'</option><option value='+second+'>'+second+'</option>');
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
            function remove(id)
            {
                $("#"+id).remove();
                var index = list.map(x => {
                return x.id;
                }).indexOf(id);
                list.splice(index, 1);
            }
        </script>
@endsection
                    
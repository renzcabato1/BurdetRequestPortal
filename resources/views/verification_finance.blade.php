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
            
            <form  method='POST' action='save/{{$sb_request->id}}' onsubmit='show();' enctype="multipart/form-data"  >
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-lg-3'>
                                <label class="font-normal">Company :  <br>{{$sb_request->company_info->name}}</label>
                            </div>
                            <div class='col-lg-3'>
                                <label class="font-normal">Department : <br>{{$sb_request->department_info->name}}</label>
                                
                            </div>
                        
                        </div>
                    </div>
                    <hr>
                    <input type='hidden' id='company_id' value='{{$sb_request->company_id}}'>
                    <hr>
                    <h3>Details</h3>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        
                                        <th>Budget Code (IO)</th>
                                        <th>Request Type</th>
                                        <th>Qty</th>
                                        <th>Unit of measure</th>
                                        <th>Unit Price</th>
                                        <th>Remaining Balance</th>
                                        <th>Material</th>
                                        {{-- <th>Date needed</th> --}}
                                        {{-- <th>Remarks</th> --}}
                                        <th>Upload IO (for creation)</th>
                                        <th>Upload Budget Info</th>
                                    </tr>
                                </thead>
                                <tbody class='form-all'>
                                    @foreach($sb_request->details as $key => $detail)
                                    <tr id='{{$detail->id}}'>
                                        <td>
                                            {{-- <select class='form-control' name='budgted[{{$detail->id}}]'   onchange='do_budget({{$detail->id}},this.value)' required>
                                                 <option></option>
                                                 <option value='Budgted' {{($detail->budgeted == "Budgted") ? "selected":"" }}>Budget Line</option>
                                                 <option value='Not Budgeted' {{($detail->budgeted == "Not Budgeted") ? "selected":"" }}>For Creation</option>
                                             </select>
                                             <br> --}}
                                             
                                         <div class='form-{{$detail->id}}'>
                                            @if($detail->budgeted == "Budgted")
                                            <input placeholder='Budget Code(IO)' id='budget-form-{{$detail->id}}' class='form-control' value='{{$detail->budget_code}}' minlength='12' maxlength='12' name='budget_line[{{$detail->id}}]' readonly>
                                            @else
                                            For Creation
                                            @endif
                                         </div>
                                     </td>
                                        <td>
                                            <label class="font-normal"> <select class='form-control' name='type_of_request[{{$detail->id}}]' id='type_request{{$detail->id}}' onchange='request_type({{$detail->id}},this.value)'  required> 
                                                <option></option>
                                                <option value='Direct Expense' {{($detail->type_of_request == "Direct Expense") ? "selected":"" }}  >Direct Expense</option>
                                                <option value='Inventoriable' {{($detail->type_of_request == "Inventoriable") ? "selected":"" }} >Inventoriable</option>
                                                <option value='Asset' {{($detail->type_of_request == "Asset") ? "selected":"" }} >Asset</option>
                                                <select></label>
                                        </td>
                                        <td><label class="font-normal"> {{$detail->qty}}</label> </td>
                                        <td>
                                            <label class="font-normal"> {{$detail->unit_of_measure}}</label> 
                                        </td>
                                       <td>  {{number_format($detail->unit_price,2)}}</td>
                                       <td>
                                           @if($detail->budgeted == "Budgted")
                                            <input id='remaining_balance{{$detail->id}}' class='form-control' type='number'  step="0.01" name='remaining_balance[{{$detail->id}}]' min='.00'  value='{{$detail->remaining_balance}}'  required>
                                            @else
                                            <input id='remaining_balance{{$detail->id}}' class='form-control' type='number'  step="0.01" name='remaining_balance[{{$detail->id}}]' min='.00'  value='{{$detail->remaining_balance}}'  readonly>
                                            @endif
                                        </td>
                                       <td>
                                           @if($detail->type_of_request == "Inventoriable")
                                           <input class='form-control' value='{{$detail->material_code}}' id='material_code{{$detail->id}}' name='material_code[{{$detail->id}}]' minlength='8' maxlength='18'  placeholder='Material Code' readonly > <br>
                                           <textarea  minlength='10' maxlength='40'  class='form-control' id='material_description1' value='' name='material_description[{{$detail->id}}]' placeholder='Material Description' readonly>{{$detail->material_description}}</textarea></td>
                                            
                                           @else
                                           <input class='form-control' value='{{$detail->material_code}}' id='material_code{{$detail->id}}' name='material_code[{{$detail->id}}]' minlength='8' maxlength='18'  placeholder='Material Code' readonly > <br>
                                           <textarea  minlength='10' maxlength='40'  class='form-control' id='material_description1' value='' name='material_description[{{$detail->id}}]' placeholder='Material Description' readonly>{{$detail->material_description}}</textarea></td>
                                            
                                           @endif
                                            @php
                                                $date = explode("-", $detail->date_needed);
                                            @endphp

                                   
                                       
                                       {{-- <td><textarea  class='form-control' value=''  readonly>{{$detail->remarks}}</textarea></td> --}}
                                       <td>
                                            @if($detail->budgeted != "Budgted")
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label">Order Type </label>
                                                <label class="col-sm-1 col-form-label"> :  </label>
                                                <div class="col-sm-7"><select class='form-control' name='order_type[{{$detail->id}}]'  placeholder='Order Type' required>
                                                    {{-- <option value=''></option> --}}
                                                    @foreach($company_info->order_types as $order_type)
                                                        <option value='{{$order_type->order_type}}'>{{$order_type->order_type}}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <input class='form-control' type='hidden' value='{{$company_info->company_info->name}}' name='company_code[{{$detail->id}}]' minlength='4' maxlength='4' placeholder='Company Code'  readonly><br>
                                           
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label">Cost Center </label>
                                                <label class="col-sm-1 col-form-label"> :    </label>
                                                <div class="col-sm-7">
                                                    <select data-placeholder="Choose Costcenter" name='cost_center[{{$detail->id}}]' class="chosen-select"   required="true" tabindex="1" required>
                                                        <option value=""></option>
                                                        @foreach($cost_centers['COSTCENTER_LIST'] as $cost)
                                                            <option  value="{{$cost->COSTCENTER}}" {{($cost->COSTCENTER == $detail->cost_center)?'SELECTED':''}} >{{$cost->COSTCENTER}} - {{$cost->COCNTR_TXT}}</option>
                                                        @endforeach
                                                    </select>
                                                    
                                                    {{-- <input class='form-control' onchange='get_value_costcenter(this.value,{{$detail->id}})' name='cost_center[{{$detail->id}}]' minlength='10' maxlength='10' placeholder='Cost Center'  required> --}}
                                                </div>
                                            </div>
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label">1st Char </label>
                                                <label class="col-sm-1 col-form-label"> :  </label>
                                                <div class="col-sm-7"><select class='form-control' id='first_value{{$detail->id}}' onchange='get_value(this.value,{{$detail->id}})' name='key[{{$detail->id}}]'  placeholder='Key'  required>
                                                    <option value=''></option>
                                                    @foreach($company_info->assign_letters as $assign_letter)
                                                        <option value='{{$assign_letter->assign_letter}}'>{{$assign_letter->assign_letter}}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label">GL Account </label>
                                                <label class="col-sm-1 col-form-label"> :  </label>
                                                <div class="col-sm-7"> <input class='form-control' onchange='get_value_gl_account(this.value,{{$detail->id}})' type='number' onKeyPress="if(this.value.length==8) return false;" id='gl_account{{$detail->id}}' name='gl_account[{{$detail->id}}]' minlength='8' maxlength='10' placeholder='GL Account'  required><br>
                                           
                                                </div>
                                            </div>
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label">Sequence Number </label>
                                                <label class="col-sm-1 col-form-label"> :  </label>
                                                <div class="col-sm-7">    <input class='form-control' onchange='get_value_seq(this.value,{{$detail->id}})'  type='text'  id='seq_number{{$detail->id}}' onKeyPress="if(this.value.length ==7) return false;" type='number' min='1'  placeholder=''  required><br>
                                          
                                                </div>
                                            </div>
                                         
                                            <input placeholder='Budget Code(IO)' id='budget_line{{$detail->id}}'  name='budget_line[{{$detail->id}}]'  id='budget-form-{{$detail->id}}' class='form-control' value='{{$detail->budget_code}}' minlength='12' maxlength='12'  readonly><br>
                                         
                                            <select  name='plant[{{$detail->id}}]' class="chosen-select"   placeholder='Plant'  required>
                                                <option value=''>Plant</option>
                                                @foreach($company_info->plant_info as $plant)
                                                    <option value='{{$plant->plant}}' {{($plant->plant == $detail->plant)?'SELECTED':''}}>{{$plant->plant}} - {{$plant->plant_name}}</option>
                                                @endforeach
                                            </select><br>
                                            @else
                                            Order Type : {{$detail->order_type}} <br>
                                            Company Code : {{$detail->company_code}} <br>
                                            GL Account : {{$detail->gl_account}} <br>
                                            Cost Center : {{$detail->cost_center}} <br>
                                            @endif
                                        </td>
                                       <td>
                                        Fiscal Year : {{$date[0]}} <br>
                                        Period : {{$date[1]}} 
                                           <input  type='hidden' value='@if($company_info->controlling_area != null){{$company_info->controlling_area->controlling_area}}@endif' class='form-control' name='controlling_area[{{$detail->id}}]' placeholder='Controlling Area' minlength='4' maxlength='4' readonly><br>
                                           {{-- {{$detail->version}} --}}
                                           @if($detail->budgeted != "Budgted")
                                          <div class="form-group  row"><label class="col-sm-4 col-form-label">Version </label>
                                            <label class="col-sm-1 col-form-label">: </label>
                                                <div class="col-sm-6"> 
                                                    <select name='version[{{$detail->id}}]' class='form-control' required>
                                                    <option value='1'>1</option>
                                                   </select>
                                                </div>
                                            </div>
                                            @else
                                            <div class="form-group  row"><label class="col-sm-4 col-form-label">Version </label>
                                                <label class="col-sm-1 col-form-label">: </label>
                                                    <div class="col-sm-6"> 
                                                        <select name='version[{{$detail->id}}]' class='form-control' required>
                                                        <option value='{{$detail->version}}'>{{$detail->version}}</option>
                                                       </select>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($detail->budgeted != "Budgted")
                                                <div class="form-group  row"><label class="col-sm-12 col-form-label">IO Description </label>
                                                    <label class="col-sm-1 col-form-label"> </label>
                                                    <div class="col-sm-12"> 
                                                        <textarea class='form-control' minlength='10' maxlength='40' type='text' name='io_description[{{$detail->id}}]' required placeholder="Order Name">{{$detail->io_description}}</textarea>
                                    
                                                    </div>
                                                </div>
                                            @else
                                                <div class="form-group  row"><label class="col-sm-12 col-form-label">IO Description </label>
                                                    <label class="col-sm-1 col-form-label"> </label>
                                                    <div class="col-sm-12"> 
                                                        <textarea class='form-control' minlength='10' maxlength='40' type='text' name='io_description[{{$detail->id}}]' readonly placeholder="Order Name">{{$detail->io_description}}</textarea>
                                    
                                                </div>
                                            </div>
                                            @endif
                                       </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    {{-- <tr>
                                        <td colspan='9'>    <a onclick='add_row()'  class="btn btn-sm btn-primary" > Add Row </a></td>
                                    </tr> --}}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-lg-12'>
                                <b class='text-danger'><i>Supporting Documents (just click to download):</i></b><br>
                                @if(count($sb_request->attachments))
                                @foreach($sb_request->attachments as $attachment)
                                <a href='{{url($attachment->file_url)}}' target='_blank'> {{$attachment->file_name}}</a> <br>
                                @endforeach
                                @else
                                <i>No Supporting Documents</i>
                                @endif
                            </div>
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-md-6'>
                                Remarks (optional):
                                <textarea name ='remarks' class='form-control' ></textarea>
                             </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-6'>
                                Attachment (optional):
                                    <input class='form-control' name='attachment' type='file'>
                             </div>
                        </div>
                    </div>
                    <div class="form-group text-right mt-5">
                        <div>
                            <button class="btn btn-primary " id='submit' type="submit"><i class="fa fa-check"></i>&nbsp;Approve</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="footer">
    </div>
    <script type='text/javascript'>
       
        function do_budget(id,value)
        {
            $("#budget-form-"+id).remove();
            if(value === "Budgted")
            {
                var new_form = "<input placeholder='Budget Code(IO)' id='budget-form-"+id+"' class='form-control' minlength='12' maxlength='12' name='budget_line["+id+"]' required>";
                $(".form-"+id).append(new_form); 

                document.getElementById('remaining_balanace'+id).removeAttribute('readonly');
                document.getElementById("remaining_balanace"+id).required = true;
                //  document.getElementById("demo").innerHTML = x;
            }
            else if(value === "Not Budgeted")
            {
                // var new_form = "<input placeholder='Cost Center(Optional)' name='cost_center["+id+"]' id='budget-form-"+id+"' class='form-control' >";
                $(".form-"+id).append(new_form); 
                document.getElementById('remaining_balanace'+id).readOnly = true;
                document.getElementById("remaining_balanace"+id).required = false;
            }
        }
        function request_type(id,value)
        {
            // alert(id);
            if(value === "Inventoriable")
            {
                document.getElementById('material_code'+id).removeAttribute('readonly');
                document.getElementById("material_code"+id).required = true;
                // document.getElementById('material_description'+id).removeAttribute('readonly');
                // document.getElementById("material_description"+id).required = true;

            }
            else
            {
                document.getElementById('material_code'+id).readOnly = true;
                document.getElementById("material_code"+id).required = false;
                // document.getElementById('material_description'+id).readOnly = true;
                // document.getElementById("material_description"+id).required = false;

            }
        }
        function zeroPad(num) {
            return num.toString().padStart(7, "0");
        }
        function get_value(value,id)
        {
            var gl_account = parseInt(document.getElementById("gl_account"+id).value); 
            var gl_account = gl_account.toString(); 
            var gl_ac = gl_account.substring(0, 4);
            var seq_number = document.getElementById("seq_number"+id).value;
            var seq_num = zeroPad(seq_number);

            if((gl_account != "") && (seq_number!= ""))
            {
                var company_id =document.getElementById("company_id").value; 
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
                        "io" : value+gl_ac+seq_num,
                        "company_id" : company_id,
                    }     ,
                    dataType: "json",   //expect html to be returned
                    success: function(data){  
                        
                    console.log(data[0]['O_AUFNR']);
                    if(data[0]['O_AUFNR'] != "")
                    {
                        swal({
                                title: "Error Message",
                                text: "This Budget Code has already existed."
                            });
                            document.getElementById("submit").disabled = true;
                        document.getElementById("myDiv").style.display="none";
                        return false;
                    }
                    else
                    {
                        document.getElementById("submit").disabled = false;
                        document.getElementById("myDiv").style.display="none";
                        return false;

                    }
                    
                    document.getElementById("myDiv").style.display="none";
                    },
                    error: function(e)
                    {
                        swal({
                                title: "Error Message",
                                text: "Please contact your system administrator"
                            });
                            document.getElementById("myDiv").style.display="none";
                            document.getElementById("submit").disabled = true;
                    }
                });

            }
            document.getElementById("budget_line"+id).value = value+gl_ac+seq_num;
        }
        function get_value_gl_account(value,id)
        {
            var company_id =document.getElementById("company_id").value; 
            var first_char = document.getElementById("first_value"+id).value; 
            var gl_account = parseInt(value); 
            var gl_account = gl_account.toString();  
            var gl_ac = gl_account.substring(0, 4);
            var seq_number = document.getElementById("seq_number"+id).value;
            var seq_num = zeroPad(seq_number);

                document.getElementById("myDiv").style.display="block";
                $.ajax({    //create an ajax request to load_page.php
                    
                    type: "GET",
                    url: "{{ url('/get-info/') }}",            
                    data: {
                        "gl_account" : value,
                        "company_id" : company_id,
                    }     ,
                    dataType: "json",   //expect html to be returned
                    success: function(data){  
                    console.log(data);
                    if(data['O_GLACCIND'] == "Y")
                    {
                        document.getElementById("myDiv").style.display="none";
                        if((first_char != "") && (seq_number!= ""))
                        {
                       
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
                            // document.getElementById("myDiv").style.display="block";
                            $.ajax({    //create an ajax request to load_page.php
                                
                                type: "GET",
                                url: "{{ url('/get-budget-info/') }}",            
                                data: {
                                    "io" : first_char+gl_ac+seq_num,
                                    "company_id" : company_id,
                                }     ,
                                dataType: "json",   //expect html to be returned
                                success: function(data){  
                                    
                                if(data[0]['O_AUFNR'] != "")
                                {
                                    swal({
                                            title: "Error Message",
                                            text: "This Budget Code is already existed"
                                        });
                                    document.getElementById("submit").disabled = true;
                                    document.getElementById("myDiv").style.display="none";
                                    return false;
                                }
                                else
                                {
                                    document.getElementById("submit").disabled = false;
                                    document.getElementById("myDiv").style.display="none";
                                    return false;

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
                        
                    }
                    else
                    {
                        swal({
                        title: "Error Message",
                        text: "Please enter valid GL Account"
                    });
                    document.getElementById("submit").disabled = true;
                    document.getElementById("myDiv").style.display="none";
                    return false;
                    }
                    },
                    error: function(e)
                    {
                        swal({
                                title: "Error Message",
                                text: "Please contact your system administrator"
                            });
                            document.getElementById("myDiv").style.display="none";
                    }
            });
           
            document.getElementById("budget_line"+id).value = first_char+gl_ac+seq_num;
        }
        function get_value_seq(value,id)
        {
            var company_id =document.getElementById("company_id").value; 
            var first_char = document.getElementById("first_value"+id).value; 
            var gl_account = parseInt(document.getElementById("gl_account"+id).value); 
            var gl_account = gl_account.toString();  
            var gl_ac = gl_account.substring(0, 4);
            var seq_number = value;
            var seq_num = zeroPad(seq_number);
            if((first_char != "") && (gl_account!= ""))
            {
              
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
                        "io" : first_char+gl_ac+seq_num,
                        "company_id" : company_id,
                    }     ,
                    dataType: "json",   //expect html to be returned
                    success: function(data){  
                        
                    console.log(data[0]['O_AUFNR']);
                    if(data[0]['O_AUFNR'] != "")
                    {
                        swal({
                                title: "Error Message",
                                text: "This Budget Code is already existed"
                            });
                            document.getElementById("submit").disabled = true;
                        document.getElementById("myDiv").style.display="none";
                        return false;
                    }
                    else
                    {
                        document.getElementById("submit").disabled = false;
                        document.getElementById("myDiv").style.display="none";
                        return false;

                    }
                    
                    document.getElementById("myDiv").style.display="none";
                    },
                    error: function(e)
                    {
                        swal({
                                title: "Error Message",
                                text: "Please contact your system administrator"
                            });
                            document.getElementById("myDiv").style.display="none";
                            document.getElementById("submit").disabled = true;
                    }
                });

            }

            document.getElementById("budget_line"+id).value = first_char+gl_ac+seq_num;
        }
        function get_value_costcenter(value,id)
        {
            var company_id =document.getElementById("company_id").value; 
          
            document.getElementById("myDiv").style.display="block";
                $.ajax({    //create an ajax request to load_page.php
                    
                    type: "GET",
                    url: "{{ url('/get-info/') }}",            
                    data: {
                        "cost_center" : value,
                        "company_id" : company_id,
                    }     ,
                    dataType: "json",   //expect html to be returned
                    success: function(data){  
                    console.log(data);
                    if(data['O_COSTCENIND'] == "Y")
                    {
                        document.getElementById("myDiv").style.display="none";
                        document.getElementById("submit").disabled = false;
                        document.getElementById("myDiv").style.display="none";
                    }
                    else
                    {
                        swal({
                        title: "Error Message",
                        text: "Please enter valid Cost Center"
                        });
                    document.getElementById("submit").disabled = true;
                    document.getElementById("myDiv").style.display="none";
                    return false;
                    }
                    },
                    error: function(e)
                    {
                        swal({
                                title: "Error Message",
                                text: "Please contact your system administrator"
                            });
                            document.getElementById("myDiv").style.display="none";
                            document.getElementById("submit").disabled = true;
                    }
        });
        }
    </script>
    @endsection
    
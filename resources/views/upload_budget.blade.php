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
    <form  method='GET'>
    <div class="row">
        <div class="col-lg-1">
        Company:
        </div>
        <div class="col-lg-3">
            <select class='form-control' name='company' >
                <option></option>
                @foreach($companies as $company)
                <option  value="{{$company->id}}"  {{($company->id == $comp) ? "selected":"" }}>{{$company->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3">
            <button class="btn btn-primary " type="submit" id='submit'><i class="fa fa-check"></i>&nbsp;Filter</button>
        </div>
    </div>
</form>
<br>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Upload IO Master</h5>
                    <button id="btnExport" onclick="exportuploadio(this)" class="btn btn-primary" style='margin-bottom:5px;'>Download </button><br>
                    @foreach($sup_details_ids as $ids)
                        <input type='hidden' name='upload_id[]' value='{{$ids}}'>
                    @endforeach
                    @foreach($create_ios_id as $creat_id)
                        <input type='hidden' name='create_id[]' value='{{$creat_id}}'>
                    @endforeach
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                        
                        {{-- {{auth()->user()->role_info()}}7 17 --}}
                        @php
                            $bus = 0;
                        @endphp
                        @foreach(auth()->user()->role_info() as $role)
                            @if(($role->company_id == 7) || ($role->company_id == 17))
                                    @php
                                        $bus = 1;
                                    @endphp
                            @endif
                        @endforeach
                        @foreach(auth()->user()->plant_info() as $plant)
                            @if(($plant->company_id == 7) || ($plant->company_id == 17))
                                    @php
                                        $bus = 1;
                                    @endphp
                            @endif
                        @endforeach
                        @if($bus == 1)
                        <table  class="table table-striped table-bordered table-hover "   id='upload_io' >
                            <thead>
                                <tr style='display:none;'>
                                    <th > "IO/Budget Line code
                                        L - LFUG
                                        52010005 - GL account
                                        003 - Cost Center series
                                        
                                        Length: 12 Characters"
                                         </th>
                                    <th > "Order Type:
                                        LFBL - LFUG Budget Line
                                        
                                        Length: 4 Characters"
                                        </th>
                                    <th > "Description of IO/Budget Line

                                        Length: 40 Characters"
                                        </th>
                                    <th > "Company Code of Requestor

                                        Length: 4 Characters"
                                          </th>
                                    <th > "Cost Center to be charged (Department where the requestor belongs)

                                        Length: 10 Characters"
                                          </th>
                                    <th > "Exeternal Order No. can be the reference number or Control No. of a form

                                        Length: 20 Characters"
                                          </th>
                                    <th >   </th>
                                    <th > "GL Account (Cost Element) to be assigned

                                        Length: 10 Characters"
                                          </th>
                                    <th > "Material to be assigned

                                        Length: 18 Characters"
                                          </th>
                                    <th > "Plant, Based on the Valuation class/Company Code

                                        Length: 6 Characters"
                                          </th>
                                </tr>
                                <tr style='display:none;'>
                                    <th > ORDER </th>
                                    <th > ORDER_TYPE</th>
                                    <th > ORDER_NAME</th>
                                    <th > COMP_CODE  </th>
                                    <th > RESPCCTR  </th>
                                    <th > EXT_ORD_NO  </th>
                                    <th > GSBER  </th>
                                    <th > ZZHKONT  </th>
                                    <th > ZZMATNR  </th>
                                    <th > WERKS  </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td > Budget Line </td>
                                    <td > Order Type</td>
                                    <td > Internal Order (IO) Description</td>
                                    <td > Company Code </td>
                                    <td > Responsible Cost Center  </td>
                                    <td > External Order Number  </td>
                                    <td > BUSINESS AREA </td>
                                    <td >GL Account </td>
                                    <td > Material </td>
                                    <td > Plant </td>
                                </tr>
                                @foreach($create_ios as $create_io)
                                <tr>
                                    <td>{{$create_io->budget_code}}</td>
                                    <td>{{$create_io->order_type}}</td>
                                    <td>{{$create_io->io_description}}</td>
                                    <td>{{$create_io->company_code}}</td>
                                    <td>{{$create_io->cost_center}}</td>
                                    <td>{{$create_io->extra_order_number}}</td>
                                    <td>{{$create_io->business_area}}</td>
                                    <td>{{$create_io->gl_account}}</td>
                                    <td>{{$create_io->material_code}}</td>
                                    <td>{{$create_io->plant}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tbody>
                            </tbody>
                        </table>
                        @else
                        <table  class="table table-striped table-bordered table-hover "   id='upload_io' >
                            <thead>
                                <tr style='display:none;'>
                                    <th > "IO/Budget Line code
                                        L - LFUG
                                        52010005 - GL account
                                        003 - Cost Center series
                                        
                                        Length: 12 Characters"
                                         </th>
                                    <th > "Order Type:
                                        LFBL - LFUG Budget Line
                                        
                                        Length: 4 Characters"
                                        </th>
                                    <th > "Description of IO/Budget Line

                                        Length: 40 Characters"
                                        </th>
                                    <th > "Company Code of Requestor

                                        Length: 4 Characters"
                                          </th>
                                    <th > "Cost Center to be charged (Department where the requestor belongs)

                                        Length: 10 Characters"
                                          </th>
                                    <th > "Exeternal Order No. can be the reference number or Control No. of a form

                                        Length: 20 Characters"
                                          </th>
                                    <th > "GL Account (Cost Element) to be assigned

                                        Length: 10 Characters"
                                          </th>
                                    <th > "Material to be assigned

                                        Length: 18 Characters"
                                          </th>
                                    <th > "Plant, Based on the Valuation class/Company Code

                                        Length: 6 Characters"
                                          </th>
                                </tr>
                                <tr style='display:none;'>
                                    <th > ORDER </th>
                                    <th > ORDER_TYPE</th>
                                    <th > ORDER_NAME</th>
                                    <th > COMP_CODE  </th>
                                    <th > RESPCCTR  </th>
                                    <th > EXT_ORD_NO  </th>
                                    <th > ZZHKONT  </th>
                                    <th > ZZMATNR  </th>
                                    <th > WERKS  </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td > Budget Line </td>
                                    <td > Order Type</td>
                                    <td > Internal Order (IO) Description</td>
                                    <td > Company Code </td>
                                    <td > Responsible Cost Center  </td>
                                    <td > External Order Number  </td>
                                    <td >GL Account </td>
                                    <td > Material </td>
                                    <td > Plant </td>
                                </tr>
                                @foreach($create_ios as $create_io)
                                <tr>
                                    <td>{{$create_io->budget_code}}</td>
                                    <td>{{$create_io->order_type}}</td>
                                    <td>{{$create_io->io_description}}</td>
                                    <td>{{$create_io->company_code}}</td>
                                    <td>{{$create_io->cost_center}}</td>
                                    <td>{{$create_io->extra_order_number}}</td>
                                    <td>{{$create_io->gl_account}}</td>
                                    <td>{{$create_io->material_code}}</td>
                                    <td>{{$create_io->plant}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tbody>
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Upload Budget Amount  </h5>
                    <button id="btnExport" onclick="exportupload_budget(this)" class="btn btn-primary" style='margin-bottom:5px;'>Download </button><br>
                    <input type='hidden' id='export_upload_io' value='{{$create_ios_id}}'>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- @include('new_account')     --}}
                        {{-- <a href='{{ url('/sb-new-request') }}'><button class="btn btn-primary" data-target="#upload_billing" data-toggle="modal" type="button"><i class="fa fa-upload"></i>&nbsp;New Request</button></a> --}}
                            
                        <table  class="table table-striped table-bordered table-hover " id='upload_budget' >
                            <thead>
                                <tr  style='display:none;'>
                                    <th >  </th>
                                    <th >COSP_VERSN</th>
                                    <th > COSP-GJAHR</th>
                                    <th > BKPF-MONAT</th>
                                    <th > COSP-OBJNR</th>
                                    <th >COSP-KTEXT if COAS-AUFNR= COSP-OBJNR </th>
                                    <th >COSP-MEG001-16</th>
                                    <th >COSP-MEINH</th>
                                    <th ></th>
                                    <th >COSP-WKG001-16 </th>
                                </tr>
                               
                            </thead>
                            <tbody>
                                <tr>
                                    <td > Controlling Area </td>
                                    <td > Version </td>
                                    <td > Fiscal Year </td>
                                    <td > Period </td>
                                    <td > Order </td>
                                    <td >  </th>
                                    <td > Quantity </td>
                                    <td > Unit </td>
                                    <td > Unit Cost </td>
                                    <td > Ttl Plan Costs COArC </td>
                                </tr>
                                
                                @foreach($sup_details as $sup_detail)
                                @php
                                    $date = explode("-", $sup_detail->date_needed);
                                @endphp

                                <tr>
                                    <td > {{$sup_detail->controlling_area}} </td>
                                    <td > {{$sup_detail->version}}  </td>
                                    <td > {{$sup_detail->year_sap}}   </td>
                                    <td > {{$sup_detail->month_sap}}   </td>
                                    <td > {{$sup_detail->budget_code}}   </td>
                                    <td > {{$sup_detail->io_description}}   </td>
                                    @if($sup_detail->type_of_request != "Direct Expense")
                                    <td > {{$sup_detail->qty}}   </td>
                                    <td > {{$sup_detail->unit_of_measure}}   </td>
                                    <td >  @if($sup_detail->qty != null ) {{$sup_detail->unit_price}}  @endif</td>
                                    @else
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    @endif
                                    <td > @if($sup_detail->qty != null ){{(($sup_detail->qty * $sup_detail->unit_price))}} @else {{$sup_detail->unit_price}}@endif </td>
                                </tr>
                                @endforeach
                               
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
<script>
    function exportuploadio(elem) 
    {
        // var ids = document.getElementById("export_upload_io").value;
        var ids = [];

              $('input[name^="create_id"]').each(function() {
                var boxvalue = $(this).val();
                ids.push(boxvalue);  
                  });
              

        var tab_text = "<table border='2px'><tr ";
            var textRange; var j = 0;
            tab = document.getElementById('upload_io');//.getElementsByTagName('table'); // id of table
            if (tab==null) {
                return false;
            }
            if (tab.rows.length == 0) {
                return false;
            }
            
            for (j = 0 ; j < tab.rows.length ; j++) 
            {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
                //tab_text=tab_text+"</tr>";
            }
            
            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
            
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");
            
            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
            {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "{{date('Y-m-d')}}_io_upload.xls");
            }
            else                 //other browser not tested on IE 11
            //sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
            try {
                var blob = new Blob([tab_text], { type: "application/vnd.ms-excel" });
                window.URL = window.URL || window.webkitURL;
                link = window.URL.createObjectURL(blob);
                a = document.createElement("a");
                if (document.getElementById("caption")!=null) {
                    a.download=document.getElementById("caption").innerText;
                }
                else
                {
                    a.download =  "{{date('Y-m-d')}}_io_upload";
                }
                
                a.href = link;
                
                document.body.appendChild(a);
                
                a.click();
                
                document.body.removeChild(a);
            } catch (e) {
            }
            document.getElementById("myDiv").style.display="block";
            $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "{{ url('/download-upload-io/') }}",            
            data: {
                "ids": ids,
            }     ,
            dataType: "json",   //expect html to be returned
            
            success: function(data){  
            location.reload();
            },
            error: function(e)
            {
                // alert(e);
                location.reload();
            }

            });
            return false;

            //return (sa);
        }
    function exportupload_budget(elem) 
    {
        var create_ids = [];

        $('input[name^="upload_id"]').each(function() {
            var boxvalue = $(this).val();
        create_ids.push(boxvalue);  
            });
        var tab_text = "<table border='2px'><tr>";
            var textRange; var j = 0;
            tab = document.getElementById('upload_budget');//.getElementsByTagName('table'); // id of table
            if (tab==null) {
                return false;
            }
            if (tab.rows.length == 0) {
                return false;
            }
            
            for (j = 0 ; j < tab.rows.length ; j++) {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
                //tab_text=tab_text+"</tr>";
            }
            
            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
            
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");
            
            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
            {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "{{date('Y-m-d')}}_budget_upload.xls");
            }
            else                 //other browser not tested on IE 11
            //sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
            try {
                var blob = new Blob([tab_text], { type: "application/vnd.ms-excel" });
                window.URL = window.URL || window.webkitURL;
                link = window.URL.createObjectURL(blob);
                a = document.createElement("a");
                if (document.getElementById("caption")!=null) {
                    a.download=document.getElementById("caption").innerText;
                }
                else
                {
                    a.download =  "{{date('Y-m-d')}}_budget_upload";
                }
                
                a.href = link;
                
                document.body.appendChild(a);
                
                a.click();
                
                document.body.removeChild(a);
            } catch (e) {
            }
            document.getElementById("myDiv").style.display="block";
            $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "{{ url('/download-upload-budget/') }}",            
            data: {
                "ids": create_ids,
            }     ,
            dataType: "json",   //expect html to be returned
            
            success: function(data){  
            // location.reload();
             location.reload();
            },
            error: function(e)
            {
                // alert(e);
                // location.reload();
                location.reload();
            }

            });
            
            return false;
            //return (sa);
        }
    </script>
@endsection

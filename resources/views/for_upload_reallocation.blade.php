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
    <div class="row">
        
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Upload Budget Amount  </h5>
                    <button id="btnExport" onclick="exportupload_budget(this)" class="btn btn-primary" style='margin-bottom:5px;'>Download </button><br>
                    <input type='hidden' id='upload_id' name='upload_id' value='{{$sup_details_ids}}'>
                    
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
                                    <th ></th>
                                    <th ></th>
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
                                    <td > From Period</td>
                                    <td > From Version </td>
                                </tr>
                                
                                @foreach($sup_details as $sup_detail)
                                @php
                                    $date = explode("-", $sup_detail->date_needed);
                                @endphp

                                <tr>
                                    <td >{{$sup_detail->controlling_area}} </td>
                                    <td > {{$sup_detail->version_to}}  </td>
                                    <td > {{$sup_detail->year_sap_to}}   </td>
                                    <td > {{$sup_detail->month_sap_to}}   </td>
                                    <td > {{$sup_detail->budget_code}}   </td>
                                    <td > {{$sup_detail->io_description}}   </td>
                                    <td > {{$sup_detail->qty}}   </td>
                                    <td > {{$sup_detail->unit_of_measure}}   </td>
                                    <td ></td>
                                    <td > {{$sup_detail->amount}}  </td>
                                    <td >{{$sup_detail->month_sap_from}} </td>
                                    <td > {{$sup_detail->version_from}} </td>
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
                sa = txtArea1.document.execCommand("SaveAs", true, "Re Allocation Budget.xls");
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
                    a.download =  "Upload Budget";
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
            url: "{{ url('/down-reallocation/') }}",            
            data: {
                "ids": create_ids,
            }     ,
            dataType: "json",   //expect html to be returned
            
            success: function(data){  
            // location.reload();
            console.log(data);
            location.reload();
            },
            error: function(e)
            
            {
                console.log(e);
                location.reload();
                // alert(e);
                // location.reload();
            }

            });
            
            return false;
            //return (sa);
        }
    </script>
@endsection

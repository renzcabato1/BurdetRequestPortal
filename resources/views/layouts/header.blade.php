<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <link rel="shortcut icon" href="{{ asset('/images/logo.ico')}}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    
    <!-- Styles -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <!-- FooTable -->
    <link href="{{ asset('bootstrap/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/footable/footable.core.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/blueimp/css/blueimp-gallery.min.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/chosen/bootstrap-chosen.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/switchery/switchery.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">

    <link href="{{ asset('bootstrap/css/plugins/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
    
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Firefox */
        input[type=number] {
            -moz-appearance:textfield;
        }
        .loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url("{{ asset('/images/3.gif')}}") 50% 50% no-repeat rgb(249,249,249) ;
            opacity: .8;
            background-size:200px 120px;
        }
        @media (min-width: 768px) {
            .modal-xl {
                width: 100%;
                max-width:1700px;
            }
        }
        body {
  /* font-weight: bold; */
  color:black;
}
    </style>
</head>
<body >
    
    <div id="wrapper">
        <div id = "myDiv" style="display:none;" class="loader">
        </div>
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <img alt="image" class="rounded-circle" style='width:48px;height:48px;' src="{{'http://10.96.4.126:8668/storage/id_image/employee_image/'.Auth::user()->employee_info()->id.'.png'}}"/>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="block m-t-xs font-bold">{{Auth::user()->employee_info()->first_name.' '.Auth::user()->employee_info()->last_name}}</span>
                                <span class="text-muted text-xs block">{{Auth::user()->employee_info()->position}} <b class="caret"></b></span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a class="dropdown-item" href="profile.html">Profile</a></li>
                                <li class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('logout') }}"  onclick="logout(); show();">Logout</a></li>
                            </ul>
                        </div>
                        <div class="logo-element">
                            {{ config('app.name', 'Laravel') }}
                        </div>
                    </li>
                    <li @if($header == 'Home') class='active' @endif>
                        <a href="{{ url('/') }}" class='active' onclick='show()' ><i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span> </a>
                    </li>
                    {{-- <li @if($header == 'Manpower Request') class='active' @endif  >
                        
                        <a href="{{ url('/manpower-request') }}"  onclick='show()'><i class="fa fa-money"></i> <span class="nav-label">Manpower Request</span></a>
                    </li> --}}
                    {{-- <li @if($header == 'For Supplemental') class='active' @endif>
                        <a href="#"><i class="fa fa-money"></i> <span class="nav-label">For Supplemental </span><span class="fa arrow"></span></a>
                        
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Upload') class='active' @endif><a href="{{ url('/upload-for-sb') }}">Upload</a></li> 
                        </ul>
                    
                    </li> --}}
                    <li @if($header == 'Supplemental Budget') class='active' @endif>
                        <a href="#"><i class="fa fa-money"></i> <span class="nav-label">Supplemental Budget</span><span class="fa arrow"></span></a>
                        
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Request') class='active' @endif><a href="{{ url('/sb-request') }}">Request</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'For approval') class='active' @endif><a href="{{ url('/sb-for-approval') }}">For Approval</a></li> 
                        </ul>
                        @php
                            $roles = auth()->user()->role_info();
                            $finance_sap = 0;
                            $finance_non_sap = 0;
                        @endphp
                        @if(!(auth()->user()->role_info())->isEmpty())
                        @foreach($roles as $role)
                            @if($role->company_info->controlling_area != null)
                                    @php
                                        $finance_sap = 1;
                                    @endphp
                            @endif
                            @if($role->company_info->controlling_area == null)
                                @php
                                    $finance_non_sap = 1;
                                @endphp
                            @endif
                            {{-- {{($role->company_info->controlling_area)}} --}}
                        @endforeach
                        @endif
                        @if(($finance_sap == 1) || (count(auth()->user()->plant_info()) != 0))
                            <ul class="nav nav-second-level collapse">
                                <li @if($subheader == 'For Finance Approval') class='active' @endif><a href="{{ url('/sb-for-approval-finance') }}">For Finance Approval </a></li> 
                            </ul>
                            <ul class="nav nav-second-level collapse">
                                <li @if($subheader == 'For upload') class='active' @endif><a href="{{ url('/for-upload') }}">For Upload</a></li> 
                            </ul>
                        @endif
                        @if($finance_non_sap == 1)
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'For Finance Approval Non SAP') class='active' @endif><a href="{{ url('/sb-for-approval-finance-non-sap') }}">For Finance Approval Non-SAP</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Approved Request Non-SAP') class='active' @endif><a href="{{ url('/for-upload-non-sap') }}">Non SAP Approved Request</a></li> 
                        </ul>
                        @endif
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Approved') class='active' @endif><a href="{{ url('/sb-approved') }}">Approved</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Cancelled') class='active' @endif><a href="{{ url('/sb-cancelled') }}">Cancelled </a></li> 
                        </ul>
                    </li>
                 
                    <li @if($header == 'Reallocation Request') class='active' @endif>
                        <a href="#"><i class="fa fa-paper-plane"></i> <span class="nav-label">Reallocation</span><span class="fa arrow"></span></a>
                        
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Request') class='active' @endif><a href="{{ url('/re-request') }}">Request</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'For approval') class='active' @endif><a href="{{ url('/re-for-approval') }}">For Approval</a></li> 
                        </ul>
                        @if(($finance_sap == 1)|| (count(auth()->user()->plant_info()) != 0))
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'For Upload') class='active' @endif><a href="{{ url('/re-for-upload') }}">For Upload</a></li> 
                        </ul>
                        @endif
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Approved') class='active' @endif><a href="{{ url('/re-approved') }}">Approved</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Cancelled') class='active' @endif><a href="{{ url('/re-cancelled') }}">Cancelled </a></li> 
                        </ul>
                    </li>
                  
                    @if(auth()->user()->account_info() != null)
                    <li @if($header == 'Settings') class='active' @endif>
                        <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">Settings</span><span class="fa arrow"></span></a>
                        
                        {{-- <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Accounts') class='active' @endif><a href="{{ url('/accounts') }}">Accounts</a></li> 
                        </ul> --}}
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Finance') class='active' @endif><a href="{{ url('/finance-company') }}">Masterlists</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Budget Codes') class='active' @endif><a href="{{ url('/budget-codes') }}">Non SAP Budget Codes</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Cluster Head') class='active' @endif><a href="{{ url('/cluster-heads') }}">Cluster Head</a></li> 
                        </ul>
                        {{-- <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Cluster Heads') class='active' @endif><a href="{{ url('/cluster-heads') }}">Cluster Heads </a></li> 
                        </ul> --}}
                    </li>
                    @endif
                    @if(($finance_sap == 1) || (count(auth()->user()->plant_info()) == 0))
                    <li  @if($header == 'Reports') class='active' @endif>
                        <a href="#"><i class="fa fa-database"></i> <span class="nav-label">Reports</span><span class="fa arrow"></span></a>
                        {{-- <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Accounts') class='active' @endif><a href="{{ url('/accounts') }}">Accounts</a></li> 
                        </ul> --}}
                        {{-- <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Company') class='active' @endif><a href="{{ url('/report-per-company') }}">Company</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Department') class='active' @endif><a href="{{ url('/report-per-cost-center') }}">Department</a></li> 
                        </ul> --}}
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Cost Center') class='active' @endif><a href="{{ url('/report-per-costcenter') }}">Cost Center</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'User') class='active' @endif><a href="{{ url('/report-per-user') }}">Per User</a></li> 
                        </ul>
                    </li>
                    @endif
                    <li  @if($header == 'Action History') class='active' @endif>
                        <a href="#"><i class="fa fa-database"></i> <span class="nav-label">Action History </span><span class="fa arrow"></span></a>
                     
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Approved Request History') class='active' @endif><a href="{{ url('/approved-request-approver') }}">Approved Request History</a></li> 
                        </ul>
                        <ul class="nav nav-second-level collapse">
                            <li @if($subheader == 'Declined Request History') class='active' @endif><a href="{{ url('/declined-request-approver') }}">Declined Request History</a></li> 
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i></a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">Welcome to {{ config('app.name', 'Laravel') }}.</span>
                        </li>
                        
                        <li>
                            <a href="{{ route('logout') }}"  onclick="logout(); show();">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <form id="logout-form"  action="{{ route('logout') }}"  method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>{{$header}}</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">{{$header}}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>{{$subheader}}</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">
                </div>
            </div>
            
            @yield('content')
        </div>
        {{-- <script> --}}
            <script type='text/javascript'>
                function show()
                {
                    document.getElementById("myDiv").style.display="block";
                }
                function logout()
                {
                    event.preventDefault();
                    document.getElementById('logout-form').submit();
                }
            </script>
           
            <script src="{{ asset('bootstrap/js/jquery-3.1.1.min.js') }}"></script>
            <script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
            <script src="{{ asset('bootstrap/js/bootstrap.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
            
            <!-- Peity -->
            <script src="{{ asset('bootstrap/js/plugins/peity/jquery.peity.min.js') }}"></script>
            
            <!-- Custom and plugin javascript -->
            <script src="{{ asset('bootstrap/js/inspinia.js') }}"></script>
            
            <!-- iCheck -->
            <script src="{{ asset('bootstrap/js/plugins/iCheck/icheck.min.js') }}"></script>
            
            <script src="{{ asset('bootstrap/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
            <!-- Peity -->
            <script src="{{ asset('bootstrap/js/demo/peity-demo.js') }}"></script>
            <!-- Chosen -->
            <script src="{{ asset('bootstrap/js/plugins/chosen/chosen.jquery.js') }}"></script>
            <!-- Flot -->
            <script src="{{ asset('bootstrap/js/plugins/flot/jquery.flot.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/flot/jquery.flot.spline.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/flot/jquery.flot.resize.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/flot/jquery.flot.pie.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/flot/jquery.flot.symbol.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/flot/jquery.flot.time.js') }}"></script>
            <link href="{{ asset('bootstrap/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
            <!-- Peity -->
            <script src="{{ asset('bootstrap/js/plugins/peity/jquery.peity.min.js') }}"></script>
            <!-- FooTable -->
            <script src="{{ asset('bootstrap/js/plugins/footable/footable.all.min.js') }}"></script>
            
            <!-- Custom and plugin javascript -->
            <script src="{{ asset('bootstrap/js/plugins/pace/pace.min.js') }}"></script>
            
            <!-- jQuery UI -->
            <script src="{{ asset('bootstrap/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
               <!-- Morris -->
            {{-- <script src="{{ asset('bootstrap/js/plugins/morris/raphael-2.1.0.min.js') }}"></script>
            <script src="{{ asset('bootstrap/js/plugins/morris/morris.js') }}"></script>
            <script src="{{ asset('bootstrap/js/demo/morris-demo.js') }}"></script> --}}
            {{-- <!-- Jvectormap -->
                <script src="{{ asset('bootstrap/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
                <script src="{{ asset('bootstrap/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
                --}}
                <!-- Datatable -->
                <script src="{{ asset('bootstrap/js/plugins/dataTables/datatables.min.js') }}"></script>
                <script src="{{ asset('bootstrap/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
                <!-- EayPIE -->
                <script src="{{ asset('bootstrap/js/plugins/easypiechart/jquery.easypiechart.js') }}"></script>
                
                <!-- Sparkline -->
                <script src="{{ asset('bootstrap/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
                
                <!-- Sparkline demo data  -->
                <script src="{{ asset('bootstrap/js/demo/sparkline-demo.js') }}"></script>
                
                <!-- Switchery -->
                <script src="{{ asset('bootstrap/js/plugins/switchery/switchery.js') }}"></script>
                <!-- Input Mask-->
                <script src="{{ asset('bootstrap/js/plugins/jasny/jasny-bootstrap.min.js') }}"></script>
                <script src="{{ asset('bootstrap/js/plugins/summernote/summernote-bs4.js') }}"></script>
                
                <!-- blueimp gallery -->
                <script src="{{ asset('bootstrap/js/plugins/blueimp/jquery.blueimp-gallery.min.js') }}"></script>
                
                <!-- Jquery Validate -->
                <script src="{{ asset('bootstrap/js/plugins/validate/jquery.validate.min.js') }}"></script>
                    <!-- Date range picker -->
                <script src="{{ asset('bootstrap/js/plugins/daterangepicker/daterangepicker.js') }}"></script>

                   <!-- Data picker -->
                <script src="{{ asset('bootstrap/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>

                <script src="{{ asset('bootstrap/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
                <script>
                     $('.chosen-select').chosen({width: "100%"});
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
                    $(document).ready(function()
                    {
                        var invalidChars = ["-", "e", "+", "E"];

                        $("input[type='number']").on("keydown", function(e){ 
                            if(invalidChars.includes(e.key)){
                                e.preventDefault();
                            }
                        });
                        var d = ("{{date('m-d-Y')}}");
                        var dateToday = new Date();
                        $('#data_5 .input-daterange').datepicker({
                            
                            keyboardNavigation: false,
                            forceParse: false,
                            autoclose: true,
                            format: 'mm-dd-yyyy',
                            startDate: '-0m',
                           
                        });
                        $('.dataTables-example').DataTable({
                            lengthMenu: [[10, 25, 50,-1], [10, 25, 50,"All"]],
                            // pageLength: -1,
                            scrollY:        true,
                            responsive: true,
                            searching: true,
                            ordering: false,
                            dom: '<"html5buttons"B>lTfgitp',
                            buttons: [
                            // { extend: 'copy'},
                            {extend: 'csv', title: '{{date("Y-m-d")}}'},
                            {extend: 'excel', title: '{{date("Y-m-d")}}'},
                            {extend: 'pdf', title: '{{date("Y-m-d")}}'},
                            
                            {
                                extend: 'print',
                                customize: function (win)
                                {
                                    $(win.document.body).addClass('white-bg');
                                    $(win.document.body).css('font-size', '10px');
                                    
                                    $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                                }
                            }]
                            
                        });
                        // var indexLastColumn = $(".company-report").find('tr')[0].cells.length-1;
  
                        $('.company-report').DataTable({
                            order:[[4,'desc']],
                            order:[[3,'desc']],
                            order:[[1,'desc']],
                            order:[[2,'desc']],
                            // lengthMenu: [[10, 25, 50,-1], [10, 25, 50,"All"]],
                            pageLength: -1,
                            scrollY:        true,
                            responsive: true,
                            searching: true,
                            // ordering: false,
                     
                            dom: '<"html5buttons"B>lTfgitp',
                            buttons: [
                            // { extend: 'copy'},
                            {extend: 'csv'},
                            {extend: 'excel', title: 'ExampleFile'},
                            {extend: 'pdf', title: 'ExampleFile'},
                            {
                                extend: 'print',
                                customize: function (win)
                                {
                                    $(win.document.body).addClass('white-bg');
                                    $(win.document.body).css('font-size', '10px');
                                    
                                    $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                                }
                            }]
                            
                        });
                        $('#companies').DataTable({
                            // lengthMenu: [[10, 25, 50,-1], [10, 25, 50,"All"]],
                      
                            pageLength: -1,
                            scrollY:        true,
                            responsive: true,
                            searching: true,
                            ordering: false,
                            paging : false,
                            columnDefs: [
                            { width: 10, targets: 0 }
                        ],
                     
                            // dom: '<"html5buttons"B>lTfgitp',
                            // buttons: [
                            // // { extend: 'copy'},
                            // {extend: 'csv'},
                            // {extend: 'excel', title: 'ExampleFile'},
                            // {extend: 'pdf', title: 'ExampleFile'},
                            
                            // {
                            //     extend: 'print',
                            //     customize: function (win)
                            //     {
                            //         $(win.document.body).addClass('white-bg');
                            //         $(win.document.body).css('font-size', '10px');
                                    
                            //         $(win.document.body).find('table')
                            //         .addClass('compact')
                            //         .css('font-size', 'inherit');
                            //     }
                            // }]
                            
                        });
                        $('#last_approver').DataTable({
                            pageLength: -1,
                            scrollY:        true,
                            responsive: true,
                            searching: true,
                            ordering: false,
                            paging : false,
                   
                            
                        });
                        $('#unit_of_measure').DataTable({
                            pageLength: -1,
                            scrollY:        true,
                            responsive: true,
                            searching: true,
                            ordering: false,
                            paging : false,
                   
                            
                        });
                    });
                    
                </script>
            </body>
            </html>
            
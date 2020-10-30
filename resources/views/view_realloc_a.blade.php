
<div class="modal" id="view_realloc{{$request->id}}" tabindex="-1" role="dialog"  >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">View Request</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='cancel-request/{{$request->id}}' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class='row'>
                        <div class='col-md-6'>
                            Company : {{$request->company_info->company_abbreviation}}
                            
                        </div>
                        <div class='col-md-6'>
                            Department : {{$request->department_info->name}}
                        </div>
                    </div>
                    <br>
                    <div class="row border-bottom border-top">
                        <div class="col-lg-2 border-right">
                            Budget Code (IO)
                        </div>
                        <div class="col-lg-1 border-right border-top border-left  ">
                            Qty
                        </div>
                        <div class="col-lg-2 border-right border-top border-left  ">
                            Amount
                        </div>
                            <div class="col-lg-2 border-right border-left">
                           From
                        </div>
                            <div class="col-lg-2 border-right border-left">
                           To
                        </div>
                        <div class="col-lg-3 border-right border-left">
                           Reason
                        </div>
                      
                    </div>
                    @php
                    $total = 0;
                    $total_sb = 0;
                    @endphp
                    @foreach($request->details as $detail)
                    <div class="row border-bottom">
                        <div class="col-lg-2 border-right">
                            {{$detail->budget_code}} 
                           
                        </div>
                      
                        <div class="col-lg-2 border-right border-right  ">
                            {{$detail->qty}}
                        </div>
                        <div class="col-lg-2 border-right border-top border-left  ">
                            {{number_format($detail->amount,2)}}
                        </div>
                        <div class="col-lg-3 border-right border-top border-left  ">
                            {{date('Y-m',strtotime($detail->date_from))}} <br>
                            Version : {{$detail->version_from}}
                        </div>
                        <div class="col-lg-3 border-right  border-top border-left  ">
                            {{date('Y-m',strtotime($detail->date_to))}} <br>
                            Version : {{$detail->version_to}}
                        </div>
                        <div class="col-lg-3 border-right  border-top border-left  ">
                             {{$detail->reason}}
                        </div>
                      
                        
                    </div>
                   
                    @endforeach
                  
                    <br>
                    <hr>
                    <b class='text-danger'><i>Supporting Documents (just click to download):</i></b><br>
                    @if(count($request->attachments))
                    @foreach($request->attachments as $attachment)
                    <a href='{{url($attachment->file_url)}}' target='_blank'> {{$attachment->file_name}}</a> <br>
                    @endforeach
                    @else
                    <i>No Supporting Documents</i>
                    @endif
                    
                    <br>
                    <br>
                    <hr>
                    <div class="row border-bottom border-top">
                        <div class="col-lg-2 border-right">
                            Employee
                        </div>
                        {{-- <div class="col-lg-2 border-right">
                            Position
                        </div> --}}
                        <div class="col-lg-2 border-right">
                            Company
                        </div>
                        <div class="col-lg-2 border-right">
                            Department
                        </div>
                        <div class="col-lg-2 border-right">
                            Status
                        </div>
                        <div class="col-lg-2 border-right">
                            Date Status 
                        </div>
                        <div class="col-lg-2 border-right">
                            Remarks
                        </div>
                    </div>
                   
                    @foreach($request->approvers_info as $approver)
                    {{-- {{dd($approver->employee_info->position)}} --}}
                    <div class="row border-bottom">
                        <div class="col-lg-2 border-right">
                            {{-- {{$approver}} --}}
                            {{$approver->user_info->name}} 
                        </div>
                        {{-- <div class="col-lg-2 border-right">
                              {{$approver->employee_info->position}} 
                         
                        </div> --}}
                        <div class="col-lg-2 border-right">
                              {{$approver->employee_info->EmployeeCompany[0]->company_abbreviation}} 
                          
                        </div>
                        <div class="col-lg-2 border-right">
                              {{$approver->employee_info->EmployeeDepartment[0]->name}} 
                         
                        </div>
                        <div class="col-lg-2 border-right mt-1">
                            @if($approver->status == "Pending") <span class="label">{{$approver->status}}</span> @endif 
                            @if($approver->status == "Approved") <span class="label label-primary">{{$approver->status}}</span> @endif 
                            @if($approver->status == "Declined") <span class="label label-danger">{{$approver->status}}</span> @endif 
                        </div>
                        <div class="col-lg-2 border-right">
                            @if($approver->status == "Pending") @else {{date('M. d, Y',strtotime($approver->date_action))}} @endif
                        </div>
                        <div class="col-lg-2 border-right">
                            {{$approver->remarks}}
                        </div>
                    </div>
                    @endforeach
                </div>
              
            </form>
        </div>
    </div>
</div>

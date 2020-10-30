
<div class="modal" id="approve_per_line{{$request->id}}" tabindex="-1" role="dialog"  >
    <div class="modal-dialog modal-xl" role="document">
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
            <form method='POST' action='action-request/{{$request->id}}' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class='row'>
                        <div class='col-md-6'>
                            Company : {{$request->sb_request->company_info->company_abbreviation}}
                            
                        </div>
                        <div class='col-md-6'>
                            Department : {{$request->sb_request->department_info->name}}
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class='row'>
                        <div class='col-md-4'>
                            Projected Disbursement Date : <br>
                            @php
                                $date_from_projected = explode("-", $request->sb_request->date_from_projected);
                                $date_to_projected = explode("-", $request->sb_request->date_to_projected);
                                $expected_delivery_date_from = explode("-", $request->sb_request->expected_delivery_date_from);
                                $expected_delivery_date_to = explode("-", $request->sb_request->expected_delivery_date_to);
                            @endphp
                            
                            {{date('M d, Y',strtotime($date_from_projected[2].'-'.$date_from_projected[0].'-'.$date_from_projected[1]))}}  - {{date('M d, Y',strtotime($date_to_projected[2].'-'.$date_to_projected[0].'-'.$date_to_projected[1]))}}
                        </div>
                        <div class='col-md-4'>
                            Expected Delivery Date :  <br>     {{date('M d, Y',strtotime($expected_delivery_date_from[2].'-'.$expected_delivery_date_from[0].'-'.$expected_delivery_date_from[1]))}}  - {{date('M d, Y',strtotime($expected_delivery_date_to[2].'-'.$expected_delivery_date_to[0].'-'.$expected_delivery_date_to[1]))}}
                        </div>
                        <div class='col-md-4'>
                            Conversion Rate Used : {{$request->sb_request->conversion_rate_used}}
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class="row border-bottom ">
                        <div class="col-lg-6 ">
                        </div>
                        <div class="col-lg-3 border-right border-left   border-top text-conter">
                            <p class="text-center">Supplemental Budget</p>
                        </div>
                    </div>
                    <div class="row border-bottom border-top">
                        <div class="col-lg-1 border-right">
                            Budget Code (IO) / Cost Center
                        </div>
                        <div class="col-lg-1 border-right border-left">
                            Request Type
                        </div>
                        <div class="col-lg-1 border-right">
                            Material Code
                        </div>
                        <div class="col-lg-1 border-right">
                            Material Description
                        </div>
                        <div class="col-lg-1 border-right">
                           Date needed / ROI
                        </div>
                        <div class="col-lg-1 border-right">
                           Remarks
                        </div>
                        <div class="col-lg-1 border-right border-left">
                            <div class='row'>
                                <div class="col-lg-6 border-right">
                               QTY
                                </div>
                                <div class="col-lg-6 border-right">
                               UOM
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 border-right">
                            Unit Price
                        </div>
                        <div class="col-lg-1 border-right">
                          Supplemental Budget
                        </div>
                        <div class="col-lg-1 border-right">
                           Remaining Balance
                        </div>
                        <div class="col-lg-1 border-right">
                            Total Amount 
                        </div>
                        <div class="col-lg-1 border-right">
                           Action 
                        </div>
                       
                    </div>
                    @php
                    $total_sb = 0;
                    $total = 0;
                    @endphp
                    @foreach($request->sb_request->details as $detail)
                    <div class="row border-bottom">
                        <div class="col-lg-1 border-right">
                            @if($detail->budget_code != null) 
                            {{$detail->budget_code}} 
                            @else 
                            For Creation
                            @endif
                            <br>
                            {{$detail->cost_center}}
                        </div>
                        <div class="col-lg-1 border-right border-left">
                            {{$detail->type_of_request}}
                        </div>
                        <div class="col-lg-1 border-right">
                            {{$detail->material_code}}
                        </div>
                        <div class="col-lg-1 border-right border-left">
                            {{$detail->material_description}}
                        </div>
                        <div class="col-lg-1 border-right">
                            {{date('M Y',strtotime($detail->date_needed))}} <br>
                            @if($detail->roi != null) <a href='{{url($detail->roi)}}' target='_blank'> File </a> @endif
                        </div>
                        <div class="col-lg-1 border-right">
                            {{$detail->remarks}}
                        </div>
                        <div class="col-lg-1 border-right">
                            <div class='row'>
                                <div class="col-lg-6 border-right">
                                {{$detail->qty}}
                                </div>
                                <div class="col-lg-6 border-right">
                                    {{$detail->unit_of_measure}}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 border-right">
                            {{number_format($detail->unit_price,2)}} 
                        </div>
                        
                        <div class="col-lg-1 border-right">
                            @if($detail->qty != null)
                                {{number_format($detail->qty * $detail->unit_price,2)}}
                            @else
                                {{number_format($detail->unit_price,2)}}
                            @endif
                        </div>
                        <div class="col-lg-1 border-right">
                             {{number_format($detail->remaining_balance ,2)}}
                        </div>
                        <div class="col-lg-1 border-right">
                             @if($detail->qty != null)
                              {{number_format(($detail->qty * $detail->unit_price) + $detail->remaining_balance ,2)}}
                              @else
                              {{number_format(($detail->unit_price) + $detail->remaining_balance ,2)}}
                              @endif
                        </div>
                        <div class="col-lg-1 border-right">
                            <select name='action[{{$detail->id}}]' onchange='action_taken({{$detail->id}},this.value)' class='form-control' required>
                                <option ></option>
                                <option value='Approved'>Approved</option>
                                <option value='Declined'>Declined</option>
                                <option value='Review Again'>Review Again</option>
                            </select>
                            <br>
                        </div>
                    </div>
                    @php
                    if($detail->qty != null)
                    {

                        $total_sb = $total_sb + ($detail->qty * $detail->unit_price);
                        $total = $total + (($detail->qty * $detail->unit_price) + $detail->remaining_balance);
                    }
                    else
                    {
                        $total_sb = $total_sb + ($detail->unit_price);
                        $total = $total + (($detail->unit_price) + $detail->remaining_balance);

                    }
                    @endphp
                    @endforeach
                    <div class="row ">
                        <div class="col-lg-6 ">
                        </div>
                        <div class="col-lg-2 border-top  ">
                            
                        </div>
                        <div class="col-lg-1 border  ">
                          {{number_format($total_sb ,2)}}
                        </div>
                        <div class="col-lg-1 ">
                        </div>
                        <div class="col-lg-1 border-right border-bottom">
                            {{number_format( $total,2)}}
                        </div>
                    </div>
                    <br>
                    <hr>
                    <b class='text-danger'><i>Supporting Documents (just click to download): </i></b><br>
                    @if(count($request->sb_request->attachments))
                    @foreach($request->sb_request->attachments as $attachment)
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
                        <div class="col-lg-2 border-right">
                            Position
                        </div>
                        <div class="col-lg-2 border-right">
                            Company
                        </div>
                        <div class="col-lg-2 border-right">
                            Department
                        </div>
                        <div class="col-lg-1 border-right">
                            Status
                        </div>
                        <div class="col-lg-1 border-right">
                            Date Status 
                        </div>
                        <div class="col-lg-2 border-right">
                            Remarks
                        </div>
                    </div>
                    @foreach($request->sb_request->approvers_info as $approver)
                    <div class="row border-bottom">
                        <div class="col-lg-2 border-right">
                            {{$approver->user_info->name}} 
                        </div>
                        <div class="col-lg-2 border-right">
                              {{$approver->employe_info->position}} 
                            {{-- @if($approver->role_number == 1)
                            Immediate Supervisor
                            @elseif($approver->role_number == 2)
                            BU Head
                            @elseif($approver->role_number == 3)
                            Cluster Head
                            @elseif($approver->role_number == 4)
                            Finance Head
                            @elseif($approver->role_number == 5)
                            Final approver
                            @endif --}}
                        </div>
                        <div class="col-lg-2 border-right">
                              {{$approver->employe_info->EmployeeCompany[0]->company_abbreviation}} 
                            {{-- @if($approver->role_number == 1)
                            Immediate Supervisor
                            @elseif($approver->role_number == 2)
                            BU Head
                            @elseif($approver->role_number == 3)
                            Cluster Head
                            @elseif($approver->role_number == 4)
                            Finance Head
                            @elseif($approver->role_number == 5)
                            Final approver
                            @endif --}}
                        </div>
                        <div class="col-lg-2 border-right">
                              {{$approver->employe_info->EmployeeDepartment[0]->name}} 
                            {{-- @if($approver->role_number == 1)
                            Immediate Supervisor
                            @elseif($approver->role_number == 2)
                            BU Head
                            @elseif($approver->role_number == 3)
                            Cluster Head
                            @elseif($approver->role_number == 4)
                            Finance Head
                            @elseif($approver->role_number == 5)
                            Final approver
                            @endif --}}
                        </div>
                        <div class="col-lg-1 border-right mt-1">
                            @if($approver->status == "Pending") <span class="label">{{$approver->status}}</span> @endif 
                            @if($approver->status == "Approved") <span class="label label-primary">{{$approver->status}}</span> @endif 
                            @if($approver->status == "Declined") <span class="label label-danger">{{$approver->status}}</span> @endif 
                        </div>
                        <div class="col-lg-1 border-right">
                            @if($approver->status == "Pending") @else {{date('M. d, Y',strtotime($approver->date_action))}} @endif
                        </div>
                        <div class="col-lg-2 border-right">
                            {{$approver->remarks}}
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type='submit'  class="btn btn-primary" >Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal" id="view{{$request->id}}" tabindex="-1" role="dialog"  >
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">View Request ( {{$request->company_info->company_abbreviation}}-{{date('Ym',strtotime($request->created_at))}}-{{str_pad($request->ref_id, 4, '0', STR_PAD_LEFT)}} )</h5>
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
                    <hr>
                    <div class='row'>
                        <div class='col-md-4'>
                            Projected Disbursement Date : <br>
                            @php
                                $date_from_projected = explode("-", $request->date_from_projected);
                                $date_to_projected = explode("-", $request->date_to_projected);
                                $expected_delivery_date_from = explode("-", $request->expected_delivery_date_from);
                                $expected_delivery_date_to = explode("-", $request->expected_delivery_date_to);
                            @endphp
                            
                            {{date('M d, Y',strtotime($date_from_projected[2].'-'.$date_from_projected[0].'-'.$date_from_projected[1]))}}  - {{date('M d, Y',strtotime($date_to_projected[2].'-'.$date_to_projected[0].'-'.$date_to_projected[1]))}}
                        </div>
                        <div class='col-md-4'>
                            Expected Delivery Date :  <br>     {{date('M d, Y',strtotime($expected_delivery_date_from[2].'-'.$expected_delivery_date_from[0].'-'.$expected_delivery_date_from[1]))}}  - {{date('M d, Y',strtotime($expected_delivery_date_to[2].'-'.$expected_delivery_date_to[0].'-'.$expected_delivery_date_to[1]))}}
                        </div>
                        <div class='col-md-4'>
                            Conversion Rate Used : {{$request->conversion_rate_used}}
                        </div>
                    </div>
                    <br>
                    <hr>
                    <br>
                    <div class="row border-bottom ">
                        <div class="col-lg-6 ">
                        </div>
                       
                        <div class="col-lg-4 border-right border-left   border-top text-conter">
                            <p class="text-center">Supplemental Budget</p>
                        </div>
                    </div>
                    <div class="row border-bottom border-top">
                        <div class="col-lg-1 border-right">
                            Budget Code (IO)
                        </div>
                        <div class="col-lg-2 border-right">
                            Material Code <br>
                            IO Description
                        </div>
                        <div class="col-lg-1 border-right">
                           Date needed
                        </div>
                        <div class="col-lg-2 border-right  ">
                           Remarks
                        </div>
                        <div class="col-lg-1 border-right border-top border-left  ">
                            Qty
                        </div>
                        <div class="col-lg-1 border-right border-top border-left  ">
                            UOM
                        </div>
                        <div class="col-lg-1 border-right border-top border-left  ">
                            Unit Price
                        </div>
                        
                        <div class="col-lg-1 border-right border-top border-left  ">
                            Total Amount <br>
                            <b><i>VAT Exclusive</i></b>
                        </div>
                        <div class="col-lg-1 border-right">
                           Remaining Balance
                        </div>
                        <div class="col-lg-1 border-right">
                            Total Cost
                        </div>
                    </div>
                    @php
                    $total = 0;
                    $total_sb = 0;
                    $total_sb_int = 0;
                    @endphp
                    @foreach($request->details as $detail)
                    <div class="row border-bottom">
                        <div class="col-lg-1 border-right">
                            @if($detail->budget_code != null) 
                            {{$detail->budget_code}} 
                            @else 
                            For Creation
                            @endif
                            <br>
                            {{$detail->type_of_request}}
                        </div>
                        <div class="col-lg-2 border-right">
                            {{$detail->material_code}}
                            <br>
                            
                            {{$detail->material_description}}
                            <br>
                            {{$detail->io_description}}
                            <br>
                        </div>
                        <div class="col-lg-1 border-right">
                            {{date('M Y',strtotime($detail->date_needed))}} <br>
                            @if($detail->roi != null)<a href='{{url($detail->roi)}}'>ROI</a>@endif
                        </div>
                        <div class="col-lg-2 border-right border-right  ">
                            {!! nl2br(e($detail->remarks))!!}
                        </div>
                        <div class="col-lg-1 border-right border-top border-left  ">
                            {{$detail->qty}}
                        </div>
                        <div class="col-lg-1 border-right border-top border-left  ">
                            {{$detail->unit_of_measure}}
                        </div>
                        <div class="col-lg-1 border-right  border-top border-left  ">
                            {{number_format($detail->unit_price,2)}}  
                        </div>
                        <div class="col-lg-1 border-right">
                            @if($detail->qty != null)
                                {{number_format($detail->qty * $detail->unit_price,2)}}
                            @else
                                {{number_format($detail->unit_price,2)}}
                            @endif
                            <br>
                            <br>
                            <span style="color:red;">VAT Inclusive <br>
                            @if($detail->no_vat == "Yes")
                                    @if($detail->qty != null)
                                        {{number_format($detail->qty * $detail->unit_price,2)}}
                                    @else
                                        {{number_format($detail->unit_price,2)}}
                                    @endif
                            @else
                                    @if($detail->qty != null)
                                        {{number_format(($detail->qty * $detail->unit_price)*1.12,2)}}
                                    @else
                                        {{number_format(($detail->unit_price)*1.12,2)}}
                                    @endif
                            @endif
                            </span>
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
                    </div>
                    @php
                    if($detail->qty != null)
                    {

                        $total_sb = $total_sb + ($detail->qty * $detail->unit_price);
                        $total = $total + (($detail->qty * $detail->unit_price) + $detail->remaining_balance);
                        if($detail->no_vat == "Yes")
                        {
                            $total_sb_int = $total_sb_int+($detail->qty*$detail->unit_price);
                        }
                        else
                        {
                            $total_sb_int = ($total_sb_int+($detail->qty * $detail->unit_price)*1.12);
                        }
                    }
                    else
                    {
                        $total_sb = $total_sb + ($detail->unit_price);
                        $total = $total + (($detail->unit_price) + $detail->remaining_balance);

                        if($detail->no_vat == "Yes")
                        {
                            $total_sb_int = $total_sb_int + ($detail->unit_price);
                        }
                        else
                        {
                            $total_sb_int = $total_sb_int + (($detail->unit_price)*1.12);
                        }

                    }
                    @endphp
                    @endforeach
                    <div class="row ">
                        <div class="col-lg-6 ">
                        </div>
                        <div class="col-lg-3 border-top  ">
                            
                        </div>
                        <div class="col-lg-1 border  ">
                          {{number_format($total_sb ,2)}}
                          <br>
                          <br>
                          <span style="color:red;">VAT Inclusive <br>
                            {{number_format($total_sb_int ,2)}} 
                        </div>
                        <div class="col-lg-1 ">
                        </div>
                        <div class="col-lg-1 border-right border-bottom">
                            {{number_format( $total,2)}}
                        </div>
                    </div>
                    
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
                    @foreach($request->approvers_info as $approver)
                    <div class="row border-bottom">
                        <div class="col-lg-2 border-right">
                            {{$approver->user_info->name}} 
                        </div>
                        <div class="col-lg-2 border-right">
                              {{$approver->employe_info->position}} 
                        </div>
                        <div class="col-lg-2 border-right">
                              {{$approver->employe_info->EmployeeCompany[0]->company_abbreviation}} 
                        </div>
                        <div class="col-lg-2 border-right">
                              {{$approver->employe_info->EmployeeDepartment[0]->name}} 
                        </div>
                        <div class="col-lg-1 border-right mt-1">
                            @if($approver->status == "Pending") <span class="label">{{$approver->status}}</span> @endif 
                            @if($approver->status == "Approved") <span class="label label-primary">{{$approver->status}}</span> @endif 
                            @if($approver->status == "Declined") <span class="label label-danger">{{$approver->status}}</span> @endif 
                            @if($approver->status == "Review Again") <span class="label label-warning">{{$approver->status}}</span> @endif 
                        </div>
                        <div class="col-lg-1 border-right">
                            @if($approver->status == "Pending") @else {{date('M. d, Y',strtotime($approver->date_action))}} @endif
                        </div>
                        <div class="col-lg-2 border-right">
                            {{$approver->remarks}}<br>
                            @if($approver->file_path) <a href='{{url($approver->file_path)}}' target='_blank'> Attachment</a>@endif 
                               
                        </div>
                    </div>
                    @endforeach
                    <br>
                    <hr>
                    <i>Additional Information <b class='text-danger'>*</b></i><br>
                    @foreach($request->approvers_info as $approver)
                    @if(count($approver->review_again_info))
                    <div class='row border-bottom border-top'>
                        <div class="col-lg-2 border-right">
                            Employee
                        </div>
                        <div class="col-lg-2 border-right">
                            Status
                        </div>
                        <div class="col-lg-2 border-right">
                            Reason
                        </div>
                        <div class="col-lg-1 border-right">
                            Date Status
                        </div>
                        <div class="col-lg-5 border-right">
                            Remarks
                        </div>
                    </div>
                        @foreach($approver->review_again_info as $attachment)
                        <div class='row border-bottom border-top'>
                            <div class="col-lg-2 border-right">
                                {{$attachment->user_info->name}}
                            </div>
                            <div class="col-lg-2 border-right">
                               @if($attachment->status == null)
                               <span class="label label-success">Replied</span>
                               @else
                               <span class="label label-warning">Review Again</span>
                               @endif
                            </div>
                            <div class="col-lg-2 border-right">
                                {{$attachment->reason}}
                            </div>
                           
                            <div class="col-lg-1 border-right">
                                {{date('M. d, Y',strtotime($attachment->date_action))}}
                            </div>
                            <div class="col-lg-5 border-right">
                                {!! nl2br(e($attachment->remarks))!!} <br>
                                @if($attachment->file_path) <a href='{{url($attachment->file_path)}}' target='_blank'> Attachment</a>@endif 
                                <br>
                            </div>
                          
                        </div>
                        @endforeach
                    @endif
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

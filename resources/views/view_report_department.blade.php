
<div class="modal" id="view{{$department->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">{{$department->name}}</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <table  class='table table-striped table-bordered table-hover company-report'>
                    
                        <tr>
                            <td>Company</td>
                            <td>Date Requested</td>
                            <td>Request By</td>
                            <td>IO description</td>
                            <td>Type of Request</td>
                            <td>Unit of Measure</td>
                            <td>Qty</td>
                            <td>Price</td>
                            <td>Total</td>
                        </tr>
                        @php
                                $total_amount = 0;
                            @endphp
                        @foreach($department->total_approved as $total_approved)
                        <tr>
                            <td>{{$total_approved->company_info->company_abbreviation}}</td>
                            <td>{{date('M. d, Y',strtotime($total_approved->created_at))}}</td>
                            <td>{{$total_approved->user_info->name}}</td>
                            
                            @foreach($total_approved->details as $key => $detail)
                                @if($key == 0)
                                        <td>
                                            {{$detail->io_description}}
                                        </td>
                                        <td>
                                            {{$detail->type_of_request}}
                                        </td>
                                        <td>
                                            {{$detail->uom}}
                                        </td>
                                        <td>
                                            {{$detail->qty}}
                                        </td>
                                        <td>
                                            {{$detail->unit_price}}
                                        </td>
                                        <td>
                                            @if($detail->qty == null)
                                            
                                                @php
                                                    $total_amount = $total_amount + ($detail->unit_price);
                                                @endphp
                                            {{number_format(($detail->unit_price),2)}}
                                            @else
                                                @php
                                                    $total_amount = $total_amount + ($detail->qty * $detail->unit_price);
                                                @endphp
                                            {{number_format(($detail->unit_price * $detail->qty),2)}}
                                            @endif
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan='3'>
                                        </td>
                                        <td>
                                            {{$detail->io_description}}
                                        </td>
                                        <td>
                                            {{$detail->type_of_request}}
                                        </td>
                                        <td>
                                            {{$detail->unit_of_measure}}
                                        </td>
                                        <td>
                                            {{$detail->qty}}
                                        </td>
                                        <td>
                                            {{$detail->unit_price}}
                                        </td>
                                        <td>
                                            @if($detail->qty == null)
                                            @php
                                                $total_amount = $total_amount + ($detail->unit_price);
                                            @endphp
                                           
                                            {{number_format(($detail->unit_price),2)}}
                                            @else
                                            {{number_format(($detail->unit_price * $detail->qty),2)}}
                                            @php
                                                $total_amount = $total_amount + ($detail->qty * $detail->unit_price);
                                            @endphp
                                            @endif
                                        </td>
                                </tr>
                                @endif
                              
                            @endforeach
                            
                        {{-- </tr> --}}
                        @endforeach
                        <tr>
                            <td colspan='8' class='text-right'>Total</td>
                            <td >{{number_format($total_amount,2)}}</td>
                        </tr>
                </table>
            </div>
        </div>
    </div>
</div>

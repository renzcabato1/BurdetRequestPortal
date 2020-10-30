
<div class="modal" id="edit_finance{{$company->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">Edit </h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='edit-finance/{{$company->id}}' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class='col-md-12'>
                      Company : {{$company->name}}
                    </div>
                    {{-- <div class='col-md-12'>
                       Finance Coordinator :
                        <select  name='finance_coor[]' class="chosen-select"  multiple required>
                            @foreach($employees as $employee)
                                <option value='{{$employee->user_id}}' {{ ( in_array($employee->user_id, $ids->toArray())) ? 'selected' : '' }}>{{$employee->first_name.' '.$employee->last_name}} </option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class='col-md-12'>
                       Finance Head :
                        <select  name='finance_head' class="chosen-select"  required>
                            <option value=''>Select employee</option>
                            @foreach($employees as $employee)
                            <option value='{{$employee->user_id}}'  @if($company->finance_heads) {{ ( $company->finance_heads->user_id == $employee->user_id) ? 'selected' : '' }} @endif >{{$employee->first_name.' '.$employee->last_name}}  </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class='col-md-12'>
                       Company Code :
                        <input class='form-control' name='company_code' value='@if($company->company_info != null){{$company->company_info->name}}@endif' type='text'  minlength='4' maxlength='4' required>
                    </div>
                    <div class='col-md-12'>
                       Controlling Area :
                        <select class='form-control' name='controlling_area' required>
                            <option @if($company->controlling_area != null){{ ( $company->controlling_area->controlling_area == "LFUG") ? 'selected' : '' }}@endif  value='LFUG'>LFUG</option>
                            <option @if($company->controlling_area != null){{ ( $company->controlling_area->controlling_area == "CSCI") ? 'selected' : '' }}@endif value='CSCI'>CSCI</option>
                            <option @if($company->controlling_area != null){{ ( $company->controlling_area->controlling_area == "1000") ? 'selected' : '' }}@endif value='1000'>1000</option>
                        </select>
                    </div>
                    <div class='col-md-12'>
                       Order Type :
                        <select class='chosen-select' name='order_types[]' multiple required>
                            <option value=''></option>
                            <option {{ ( in_array("LFBL", $order_types_ids->toArray())) ? 'selected' : '' }} value='LFBL'>LFBL</option>
                            <option {{ ( in_array("LFCX", $order_types_ids->toArray())) ? 'selected' : '' }}  value='LFCX'>LFCX</option>
                            <option {{ ( in_array("1200", $order_types_ids->toArray())) ? 'selected' : '' }}  value='1200'>1200</option>
                            <option {{ ( in_array("1600", $order_types_ids->toArray())) ? 'selected' : '' }}  value='1600'>1600</option>
                            <option {{ ( in_array("1900", $order_types_ids->toArray())) ? 'selected' : '' }}  value='1900'>1900</option>
                            <option {{ ( in_array("2000", $order_types_ids->toArray())) ? 'selected' : '' }}  value='2000'>2000</option>
                            <option {{ ( in_array("1500", $order_types_ids->toArray())) ? 'selected' : '' }}  value='1500'>1500</option>
                            <option {{ ( in_array("PFMC", $order_types_ids->toArray())) ? 'selected' : '' }}  value='PFMC'>PFMC</option>
                            <option {{ ( in_array("CSCI", $order_types_ids->toArray())) ? 'selected' : '' }}  value='CSCI'>CSCI</option>
                            <option {{ ( in_array("MGCO", $order_types_ids->toArray())) ? 'selected' : '' }}  value='MGCO'>MGCO</option>
                        </select>
                    </div>
                    <div class='col-md-12'>
                       Assign First Characters :
                        <select class='chosen-select' name='assign_characters[]' multiple required>
                            <option value=''></option>
                            <option {{ ( in_array("A", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='A'>A</option>
                            <option {{ ( in_array("M", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='M'>M</option>
                            <option {{ ( in_array("L", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='L'>L</option>
                            <option {{ ( in_array("F", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='F'>F</option>
                            <option {{ ( in_array("Z", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='Z'>Z</option>
                            <option {{ ( in_array("T", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='T'>T</option>
                            <option {{ ( in_array("I", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='I'>I</option>
                            <option {{ ( in_array("P", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='P'>P</option>
                            <option {{ ( in_array("M", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='M'>M</option>
                            <option {{ ( in_array("S", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='S'>S</option>
                            <option {{ ( in_array("D", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='D'>D</option>
                            <option {{ ( in_array("C", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='C'>C</option>
                            <option {{ ( in_array("M", $assign_letters_ids->toArray())) ? 'selected' : '' }} value='M'>M</option>
                        </select>
                    </div> --}}
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type='submit'  class="btn btn-primary" >Submit</button>
                </div>
            </form>
            
        </div>
    </div>
</div>

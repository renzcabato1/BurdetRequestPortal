
<div class="modal" id="new_coo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel"> New COO</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='new-coo' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class='col-md-12'>
                        Company:
                        <select data-placeholder="Choose Company" name='company' class="chosen-select "   id='company_id' required="true" tabindex="1" required>
                            {{-- <option value=""></option> --}}
                            @foreach($companies as $company)
                            <option  value="{{$company->id}}">{{$company->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class='col-md-12'>
                        Employee:
                        <select  name='employee' class="chosen-select"  required>
                            <option value=''>Select employee</option>
                            @foreach($employees as $employee)
                            <option value='{{$employee->user_id}}'  @if($company->finance_heads) {{ ( $company->finance_heads->user_id == $employee->user_id) ? 'selected' : '' }} @endif >{{$employee->first_name.' '.$employee->last_name}}  </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type='submit'  class="btn btn-primary" >Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

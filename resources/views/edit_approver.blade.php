
<div class="modal" id="edit_approver{{$approver->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">Edit Approver</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='edit-approver/{{$approver->id}}' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    
                    <div class='col-md-12'>
                       Employee :
                        <select  name='employee' class="chosen-select"  required>
                            {{-- <option value=''>Select employees</option> --}}
                            @foreach($employees as $employee)
                                <option value='{{$employee->user_id}}' {{ ( $approver->user_id == $employee->user_id) ? 'selected' : '' }}>{{$employee->first_name.' '.$employee->last_name}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class='col-md-12'>
                       Sign :
                      <select class='form-control' name='sign' required>
                          <option></option>
                          <option value='>' {{ ( $approver->sign == '>') ? 'selected' : '' }}>></option>
                          <option value='<' {{ ( $approver->sign == '<') ? 'selected' : '' }}><</option>
                      </select>
                    </div>
                    <div class='col-md-12'>
                       Amount :
                        <input type='number' class='form-control' name='amount'  step="0.01" value='{{$approver->amount}}' required>
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

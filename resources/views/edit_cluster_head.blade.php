
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
            <form method='POST' action='edit-cluster_head/{{$company->id}}' onsubmit='show();'  enctype="multipart/form-data" >
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
                       Cluster Head :
                        <select  name='finance_head' class="chosen-select"  required>
                            <option value=''>Select employee</option>
                            @foreach($employees as $employee)
                            <option value='{{$employee->user_id}}'  >{{$employee->first_name.' '.$employee->last_name}}  </option>
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


<div class="modal" id="upload_sb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel"> Upload for SB</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='upload-sb' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    
                    <div class='col-md-12'>
                       Employee :
                       <select data-placeholder="Choose Approver" name='additional_approvers[]' class="chosen-select "   id='company_id' required="true" tabindex="1" reqiured>
                        <option value=""></option>
                        @foreach($employees as $employee)
                        <option  value="{{$employee->user_id}}">{{$employee->first_name}} {{$employee->last_name}}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class='col-md-12'>
                       File :
                        <input class='form-control' name='file' type='file' required>
                    </div>
                    <div class='col-md-12'>
                       Remarks :
                        <textarea class='form-control' name='remarks' required></textarea>
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

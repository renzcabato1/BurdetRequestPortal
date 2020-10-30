
<div class="modal" id="new_request" tabindex="-1" role="dialog"  >
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">New Request</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='get' action='new-request-realloc' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{-- {{ csrf_field() }} --}}
                    <div class='col-md-12'>
                        Request :
                        <select name ='req' class='form-control' required>
                            <option></option>
                            {{-- <option value='Request for other BUSINESS UNIT'>Request for other BUSINESS UNIT</option> --}}
                            <option value='a'>No Endorsement Needed</option>
                            <option value='b'>Need endorsement </option>
                    
                        </select>
                     </div>
                    <div class='col-md-12'>
                        Company :
                        <select name='company' class='form-control chosen-select ' required>
                            <option></option>
                            @foreach($companies as $company)
                            <option value='{{$company->id}}'>{{$company->name}}</option>
                            {{-- <option value='Non-SAP'>Non-SAP</option> --}}
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

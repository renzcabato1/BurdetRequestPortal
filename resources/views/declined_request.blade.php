
<div class="modal" id="declined{{$request->id}}" tabindex="-1"  >
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">Decline Request</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='declined-request/{{$request->id}}' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class='col-md-12'>
                       Reason :
                       <select name ='reason' class='form-control' required>
                           <option></option>
                           @foreach($reasons as $reason)
                                <option value='{{$reason->reason}}'>{{$reason->reason}}</option>
                           @endforeach
                       </select>
                    </div>
                    <div class='col-md-12'>
                       Remarks :
                       <textarea name ='remarks' class='form-control' required></textarea>
                    </div>
                    <div class='col-md-12'>
                        Attachment (optional):
                        <input class='form-control' name='attachment' type='file'>
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

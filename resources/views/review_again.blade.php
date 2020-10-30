
<div class="modal" id="review_again{{$request->id}}" tabindex="-1"  >
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel">Review Again</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='review-again-request/{{$request->id}}' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class='col-md-12'>
                       Reason :
                       <select name ='reason' class='form-control' required>
                           <option value=''></option>
                           <option value='Do you really need this?'>Do you really need this?</option>
                           <option value='Re-negotiate price'>Re-negotiate price</option>
                           <option value='Review attached ROI'>Review attached ROI</option>
                           <option value='Review computation'>Review computation</option>
                           <option value='Others'>Others</option>
                       </select>
                    </div>
                    <div class='col-md-12'>
                       Additional Remarks :
                       <textarea name ='remarks' class='form-control' required></textarea>
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

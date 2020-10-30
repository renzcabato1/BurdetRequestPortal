
<div class="modal" id="new_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='col-md-10'>
                    <h5 class="modal-title" id="exampleModalLabel"> New Data</h5>
                </div>
                <div class='col-md-2'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method='POST' action='new-data' onsubmit='show();'  enctype="multipart/form-data" >
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class='col-md-12'>
                        Company:
                        <select data-placeholder="Choose Company" name='company' class="chosen-select "   id='company_id' required="true" tabindex="1" required>
                            <option value=""></option>
                            @foreach($companies as $company)
                            <option  value="{{$company->id}}">{{$company->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class='col-md-12'>
                        Budget Code:
                       <input class='form-control' name='budget_code' type='text' required>
                    </div>
                    <div class='col-md-12'>
                        Budget Description:
                       <textarea class='form-control' name='budget_description' required></textarea>
                    </div>
                    <div class='col-md-12'>
                        Material Description:
                       <textarea class='form-control' name='material_description' required></textarea>
                    </div>
                    <div class='col-md-12'>
                        Cost Center:
                       <select  class='form-control' name='cost_center' required>
                           <option ></option>
                           @foreach($cost_centers as $cost_center)
                           <option value='{{$cost_center->dept_name}}'>{{$cost_center->dept_name}}</option>
                           @endforeach
                       </select>
                    </div>
                    <div class='col-md-12'>
                        Unit of Measure:
                       <select  class='form-control' name='unit_of_measure' required>
                           <option ></option>
                           @foreach($unit_of_measures as $uom)
                           <option value='{{$uom->name}}'>{{$uom->name}}</option>
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

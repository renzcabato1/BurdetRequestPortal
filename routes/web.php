<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::group( ['middleware' => 'auth'], function()

{
    
Route::get('/','HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');



//sup budget
Route::get('sb-request','BudgetController@request');
Route::get('review-again','BudgetController@review_again_request');
Route::get('sb-for-approval','BudgetController@forApproval');
Route::get('sb-for-approval-finance','BudgetController@for_approval_finance');
Route::get('sb-new-request','BudgetController@sb_request')->name('sb_request');
Route::get('sb-new-request-non-sap','BudgetController@sb_request_nonsap')->name('sb_request_non_sap');
Route::get('verify/{id}','BudgetController@for_finance_verification');
Route::post('save-sb-request','BudgetController@save_request');
Route::post('re-file-sb-request/save-sb-request/{id}','BudgetController@save_refile_request');
Route::post('re-file-sb-request/save-sb-request-non-sap-refile/{id}','BudgetController@save_refile_request_non_sap');
Route::post('refile-request/save-refile-request/{id}','BudgetController@save_re_alloc_refile');



Route::post('save-sb-request-non-sap','BudgetController@save_request_non_sap');
Route::post('cancel-request/{id}','BudgetController@cancel_request');
Route::get('sb-approved','BudgetController@approved_requests');
Route::get('sb-cancelled','BudgetController@cancel_requests');
Route::post('approve-request/{id}','BudgetController@approve_request');
Route::post('declined-request/{id}','BudgetController@declined_request');
Route::get('for-upload','BudgetController@for_upload');
Route::get('re-for-upload','BudgetController@for_upload_reallocation');
Route::get('/download-upload-budget','BudgetController@download_upload_budget');
Route::get('/down-reallocation','BudgetController@down_reallocation');
Route::get('/download-upload-io','BudgetController@download_upload_io');
Route::get('re-file-sb-request/{id}','BudgetController@re_file_sup_budget');
Route::get('refile-request/{id}','BudgetController@re_file_re_alloc');


//finance_maintenance
Route::get('finance-company','FinanceController@finance_view');
Route::post('edit-finance/{company_id}','FinanceController@finance_edit');

Route::post('verify/save/{id}','BudgetController@save_verify');

//Unit_of_measure
Route::post('new-unit-of_measure','FinanceController@new_unit_of_measure');
Route::post('edit-unit-of_measure/{id}','FinanceController@edit_unit_of_measure');

//latest_approver
Route::post('edit-approver/{id}','FinanceController@edit_approver');


Route::get('get-budget-info','BudgetController@get_info');
Route::get('get-material-info','BudgetController@get_material_info');
Route::get('get-info','BudgetController@get_infor');
Route::get('get-cost-center','BudgetController@get_cost_center');
Route::get('upload-for-sb','UploadController@for_sb');


//reports
Route::get('report-per-company','BudgetController@reportspercompany');
Route::get('report-per-cost-center','BudgetController@reportsperdepartment');
Route::get('report-per-costcenter','BudgetController@reportspercostcenter');
Route::get('report-per-user','BudgetController@reportperuser');


Route::get('sb-new-request-confirm','BudgetController@confirmation');

//realloc
Route::get('re-request','BudgetController@re_alloc')->name('re_request');;
Route::get('new-request-realloc','BudgetController@re_alloc_request');
Route::post('save-realloc-request','BudgetController@save_realloc');
Route::post('cancel-request-realloc/{id}','BudgetController@cancel_request_alloc');
Route::get('re-cancelled','BudgetController@cancel_requests_reallocation');
Route::get('re-approved','BudgetController@approved_requests_reallocation');
Route::get('re-for-approval','BudgetController@re_for_approval');
Route::post('declined-request-realloc/{id}','BudgetController@re_reallocation_declined');
Route::post('review-again-request/{id}','BudgetController@review_again');
Route::post('approve-request-reallocation/{id}','BudgetController@reallocation_approved');
Route::post('new-coo','FinanceController@new_coo');
Route::post('new-endorsement','FinanceController@new_endorsement');

Route::get('sb-for-approval-finance-non-sap','BudgetController@finance_for_approval_non_sap');
Route::get('for-upload-non-sap','BudgetController@for_upload_non_sap');

Route::get('budget-codes','BudgetController@budget_codes');

Route::get('approved-request-approver','BudgetController@approved_history');
Route::get('declined-request-approver','BudgetController@declined_history');
Route::get('review-again-history','BudgetController@review_again_history');

Route::get('manual-email','BudgetController@manual_email_follow_up');
Route::get('manual-email-realloc','BudgetController@manual_email_follow_up_re_alloc');
Route::get('cluster-heads','ClusterHeadController@view_cluster_heads');

Route::post('reply-request/{id}','BudgetController@reply_review_again');
Route::post('edit-cluster_head/{id}','ClusterHeadController@edit_cluster_head');

}
);

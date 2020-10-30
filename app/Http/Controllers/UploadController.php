<?php

namespace App\Http\Controllers;
use App\Upload;
use App\Employee;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    //
    public function for_sb()
    {
        $uploads = Upload::get();
        $employees = Employee::where('status','=','Active')->orderBy('first_name','asc')->get();
        
        return view('uploads',array(

            'uploads' => $uploads,
            'employees' => $employees,
            'subheader' => 'Upload',
            'header' => 'For Supplemental',
        ));
    }
}

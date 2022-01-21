<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
class PdfController extends Controller
{
    public function index() 

    { 

        return view('pdf.index'); 

    } 

    public function create() 

    { 

        $pdf = PDF::loadView('pdf.pdf'); 

 

        $path = public_path('pdf/'); 

        $fileName =  'hasan.pdf' ; 

        $pdf->save($path . '/' . $fileName); 

 

        $pdf = public_path('pdf/'.$fileName); 

        return response()->download($pdf); 

    } 

}

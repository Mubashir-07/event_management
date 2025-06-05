<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequisitionController extends Controller
{
    public function index()
    {
        return view('requisition.index');
    }
    public function create(){
        return view('requisition.create');
    }
}

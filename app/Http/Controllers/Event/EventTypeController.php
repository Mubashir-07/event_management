<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    public function index()
    {
        return view('event-type.index');
    }
    public function create(){
        return view('event-type.create');
    }
}

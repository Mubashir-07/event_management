<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        return view('event.index');
    }

    public function create()
    {
        return view('event.create');
    }

    public function edit(Event $event)
    {
        $data = ['event'  => $event];
        return view('event.edit', $data);
    }
}

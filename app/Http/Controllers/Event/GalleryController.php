<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Event $event)
    {
        return view('gallery.index', compact('event'));
    }
    public function create(Event $event){
        return view('gallery.create', compact('event'));
    }
}

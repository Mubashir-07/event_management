<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function index()
    {
        return view('invitation.index');
    }
}

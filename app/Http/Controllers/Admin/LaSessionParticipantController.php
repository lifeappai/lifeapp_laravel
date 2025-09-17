<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaSession;
use Illuminate\Http\Request;

class LaSessionParticipantController extends Controller
{
    public function index(Request $request, LaSession $laSession)
    {
        $participants = $laSession->laSessionParticipant();
        $participants = $participants->paginate(50);
        return view('pages.admin.session-participant.index', compact('participants','request'));
    }
}

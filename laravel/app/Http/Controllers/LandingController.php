<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('public.landing');
    }
}

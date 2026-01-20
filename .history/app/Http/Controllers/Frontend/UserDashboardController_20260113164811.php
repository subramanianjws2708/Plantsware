<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('viewuserdashboard');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * TODO: Create Hand Held Launch Page
     * Show the Hand Held Launch Page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function handHeldLaunch()
    {
        return view('hand-held-launch');
    }

}

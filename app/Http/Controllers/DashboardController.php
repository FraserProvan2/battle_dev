<?php

namespace App\Http\Controllers;

use App\Battle;
use App\Events\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\ServiceProvider;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // checks if users in current battle, if so get battle data (TEMP)
        $current_battle = null;
        if(Auth::user() && Auth::user()->getBattle()) {
            $current_battle = Auth::user()->getBattle();
            // Test::dispatch(Auth::user()->getBattle());
        }
    
        return view('dashboard', [
            'current_battle' => $current_battle
        ]);
    }
}

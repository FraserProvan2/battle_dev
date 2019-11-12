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
        $data = [
            'assets' => [
                'hp_stat_icon' => asset('/images/stat_hp.svg'),
                'attack_stat_icon' => asset('/images/stat_attack.svg'),
                'speed_stat_icon' => asset('/images/stat_speed.svg')
            ],
            'user' => Auth::user()
        ];

        return view('dashboard', [
            'load_data' => json_encode($data, JSON_UNESCAPED_SLASHES),
        ]);
    }
}

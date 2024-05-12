<?php

namespace App\Http\Controllers;

use App\Models\LoginPoint;
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            // return 'admin';
            return redirect()->route('admin.home');
        }else{
            $this->loginPoints(auth()->user());
            return redirect('timeline');
            // return redirect()->route('user.home');
        }
       
        // return redirect('timeline');
        // return view('home');
    }

    public function userHome(){
        $this->loginPoints(auth()->user());
        
        return view('user.home');
    }

    public static function loginPoints($user){
        
        $date = \Carbon\Carbon::today()->toDateString();
        
        $check = LoginPoint::where('user_id', $user->id)->where('date', $date)->first();
        
        if(!$check)
        {
            LoginPoint::create(['user_id' => $user->id, 'date' => $date, 'point' => '20']);
        }
    }
}

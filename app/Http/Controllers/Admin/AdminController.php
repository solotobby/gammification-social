<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function home(){
        $res = securityVerification();
        if($res == 'OK'){
            return view('admin.home');
        }
       
    }

    
}
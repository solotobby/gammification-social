<?php

namespace App\Http\Controllers;

use App\Models\AdminLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    public function login($code){
        $chek = AdminLogin::where('code', $code)->first();
        $loc = ipLocation();
        if($loc['ip'] === $chek->ip){

            $res = securityVerification();

            if($res == 'OK'){
                return view('auth.admin_login');
            }

           
        }
        
    }

    public function proAdminLogin(Request $request){
        $res = securityVerification();
        if($res == 'OK'){
             $user = User::where(['email' => $request->email])->first();
            
            if(Hash::check($request->password, $user->password)){
                if($res == 'OK'){
                    //futher check
                    Auth::login($user);
                    if($user->hasRole('admin')){
                        return redirect()->route('admin.home');
                    }
                }else{
                    return redirect('error');
                }
                
            }else{
                return redirect('error');
            }
        }
    }
}

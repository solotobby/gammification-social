<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userList(){
        $res = securityVerification();
        if($res == 'OK'){
            $users = User::role('user')->orderBy('created_at', 'desc')->get();
            return view('admin.user.userlist', ['users' => $users]);
        }
    }
}

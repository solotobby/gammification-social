<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Models\LoginPoint;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function regUser(Request $request){
        // return $request;
        $validated = $request->validate([
             'username' => ['required', 'string', 'min:5', 'max:255', 'unique:users'],
             // 'phone' => ['numeric', 'max:255', 'unique:users'],
             'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
             // 'password' => ['required', 'string', 'min:8', 'confirmed'],
             'password' => ['required', 'string', 'min:8'],
             'access_code' => ['required', 'string']
         ]);
 
         // return $validated;
 
          $accessCode = AccessCode::where('code', $validated['access_code'])->where('is_active', true)->first();
         if($accessCode){
             $accessCode->is_active = false;
             $accessCode->save();
         }else{
             return back()->with('error', 'Invalid Access Code');
         }
 
 
         $user = User::create([
             // 'name' => $data['name'],
             'username' => $validated['username'],
             // 'phone' => $data['phone'],
             'referral_code' => Str::random(7),
             'email' => $validated['email'],
             'password' => Hash::make($validated['password']),
             'access_code_id' => $accessCode->id
         ]);
       
     
         $roleId = Role::where('name', 'user')->first()->id;
         $user->assignRole($roleId);
         
         Wallet::create(['user_id' => $user->id, 'balance' => '0.00', 'currency' => 'USD', 'level' => 'Freemuim']);
        
         if ($user) {
             Auth::login($user);
             return redirect('home');
         }
 
     }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
       
        // return User::create([
        //     'name' => $data['name'],
        //     'email' => $data['email'],
        //     'username' => 'user'.rand(10000, 1000000),
        //     'phone' => $data['phone'],
        //     'password' => Hash::make($data['password']),
        // ]);

        // $accessCode = AccessCode::where('code', $validated['access_code'])->where('is_active', true)->first();
        // if($accessCode){
        //     $accessCode->is_active = false;
        //     $accessCode->save();
        // }else{
        //     return back()->with('error', 'Invalid Access Code');
        // }


    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Models\Level;
use App\Models\LoginPoint;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
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

    public function reg(Request $request)
    {
        $validated = $request->validate([
            'referral_code' => ['sometimes', 'string', 'max:255']
        ]);

        //  return $validated;
        return view('auth.register', ['ref' => $validated['referral_code']]);
    }

    

    public function regUser(Request $request)
    {
    
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3'],
            //  'username' => ['required', 'string', 'min:5', 'max:255', 'unique:users'],
            'phone' => ['numeric', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password' => ['required', 'string', 'min:8'],
            // 'access_code' => ['required', 'string'],
            'referral_code' => ['sometimes']
        ]);

        // return $validated;

        if (!empty($validated['referral_code'])) {
            //validate referral code
            $refffff = User::where(['referral_code' => $validated['referral_code']])->first();

            if (!$refffff) {
                return back()->with('error', 'Invalid Referral Code');
            }
        }


        $user = User::create([
            'name' => $validated['name'],
            'username' => 'user' . rand(1000, 10000000), //$validated['username'],
            'phone' => $validated['phone'],
            'referral_code' => Str::random(7),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // 'access_code_id' => $accessCode->id
        ]);

        if($user){
            
            $level = Level::where('name', 'Basic')->first();
            UserLevel::create(['user_id' => $user->id, 'level_id' => $level->id]);
            $user->level_id = $level->id;
            $user->save();  

            $roleId = Role::where('name', 'user')->first()->id;
            $user->assignRole($roleId);

            Wallet::create(['user_id' => $user->id, 'balance' => $level->reg_bonus, 'promoter_balance' => '0.00', 'referral_balance' => '0.00', 'currency' => 'USD', 'level' => $level->name]);

            //Auth::guard('web')->login($user);

            Auth::login($user);
            return redirect('home');


        }


    }

    public function loginUser(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // return $validated;


            // Auth::login($user);
            // dd(Auth::check());

        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            // Authentication passed...
              session()->regenerate();
            return redirect()->intended('home');
        } else {
            return back()->with('error', 'Invalid Login Credentials');
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

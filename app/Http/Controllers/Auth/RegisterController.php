<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccessCodeMail;
use App\Models\AccessCode;
use App\Models\Level;
use App\Models\LoginPoint;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;


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
            'name' => 'required|string|min:3',
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'referral_code' => 'nullable|string'
        ]);


        DB::transaction(function () use ($validated, $request) {

            // Validate referral code once
            $referrer = null;
            if (!empty($validated['referral_code'])) {
                $referrer = User::where('referral_code', $validated['referral_code'])->firstOrFail();
            }

            $level = Level::where('name', 'Basic')->firstOrFail();

            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'referral_code' => Str::random(7),
                'password' => Hash::make($validated['password']),
            ]);

            UserLevel::create([
                'user_id' => $user->id,
                'level_id' => $level->id,
                'plan_name' => $level->name,
                'next_payment_date' => now()->addYear(),
            ]);

            $user->assignRole('user');

            Wallet::create([
                'user_id' => $user->id,
                'balance' => $level->reg_bonus,
                'promoter_balance' => 0,
                'referral_balance' => 0,
                'currency' => 'USD',
                'level' => $level->name
            ]);

            $accessCode = AccessCode::create([
                'tx_id' => time() . rand(1000, 9000),
                'name' => $level->name,
                'email' => $validated['email'],
                'amount' => $level->amount,
                'code' => generateCode(7),
                'level_id' => $level->id,
                'is_active' => false
            ]);

            $user->update(['access_code_id' => $accessCode->id]);

            // Create referral record
            if ($referrer) {
                Referral::create([
                    'user_id' =>  $user->id,  //new user  
                    'referral_id' =>$referrer->id //referred new user
                ]);
            }

            event(new Registered($user));

            // Mail::to($validated['email'])->send(new AccessCodeMail($accessCode->code, $user));

            Auth::login($user);
        });

        return redirect('home');
    }



    // public function regUser(Request $request)
    // {

    //     $validated = $request->validate([
    //         'name' => ['required', 'string', 'min:3'],
    //         'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
    //         // 'phone' => ['numeric', 'unique:users'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         // 'password' => ['required', 'string', 'min:8', 'confirmed'],
    //         'password' => ['required', 'string', 'min:8'],
    //         // 'access_code' => ['required', 'string'],
    //         'referral_code' => ['sometimes']
    //     ]);

    //     // return $validated;

    //     if (!empty($validated['referral_code'])) {
    //         //validate referral code
    //         $refffff = User::where(['referral_code' => $validated['referral_code']])->first();

    //         if (!$refffff) {
    //             return back()->with('error', 'Invalid Referral Code');
    //         }
    //     }


    //     $user = User::create([
    //         'name' => $validated['name'],
    //         'username' => 'user' . rand(1000, 10000000), //$validated['username'],
    //         // 'phone' => $validated['phone'],
    //         'referral_code' => Str::random(7),
    //         'email' => $validated['email'],
    //         'username' => $validated['username'],
    //         'password' => Hash::make($validated['password']),
    //         // 'access_code_id' => $accessCode->id
    //     ]);

    //     if ($user) {

    //         $level = Level::where('name', 'Basic')->first();
    //         $userLevel = UserLevel::create(['user_id' => $user->id, 'level_id' => $level->id]);
    //         $userLevel->level_id = $level->id;
    //         $userLevel->plan_name = $level->name;
    //         $userLevel->next_payment_date = Carbon::now()->addDays(365);
    //         $userLevel->save();

    //         $roleId = Role::where('name', 'user')->first()->id;
    //         $user->assignRole($roleId);

    //         Wallet::create(['user_id' => $user->id, 'balance' => $level->reg_bonus, 'promoter_balance' => '0.00', 'referral_balance' => '0.00', 'currency' => 'USD', 'level' => $level->name]);

    //         $code = generateCode(7);
    //         $ref = time() . rand(1000, 9000);
    //         $accessCode = AccessCode::create(['tx_id' => $ref, 'name' => $level->name, 'email' => $request->email, 'amount' => $level->amount, 'code' => $code, 'level_id' => $level->id, 'is_active' => false]);

    //         $user->access_code_id = $accessCode->id;
    //         $user->save();

    //         if($validated['referral_code']){
    //             $refffff = User::where(['referral_code' => $validated['referral_code']])->first();
    //             Referral::create(['user_id' => $user->id, 'referral_id' => $refffff->id]);
    //         }

    //         if ($accessCode) {

    //             Mail::to($request->email)->send(new AccessCodeMail($code, $user)); //send access code mail

    //             Auth::login($user);
    //             return redirect('home');
    //         }
    //     }
    // }



    public function loginUser(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            // Authentication passed...
            session()->regenerate();
            // if (auth()->user()->email_verified_at == null) {
            //     $this->verifyExistingUserEmail(auth()->user());

            //     return redirect()->intended('home')->with('info', 'Please verify your email. An access code has been sent to your email');
            // }

            return redirect()->intended('home');
        } else {
            return back()->with('error', 'Invalid Login Credentials');
        }
    }


    private function verifyExistingUserEmail($user)
    {
        $code = generateCode(7);
        $updatedCode = AccessCode::where('email', $user->email)->first();
        if ($updatedCode) {
            $updatedCode->code = $code;
            $updatedCode->is_active = false;
            $updatedCode->save();
        } else {
            $level = Level::where('id', $user->level_id)->first();
            $ref = time() . rand(1000, 9000);
            $updatedCode = AccessCode::create(['tx_id' => $ref, 'name' => $level->name, 'email' => $user->email, 'amount' => $level->reg_bonus, 'code' => $code, 'level_id' => $level->id, 'is_active' => false]);
        }

        $level = Level::where('id', $user->level_id)->first();
        $nextPaymentDate = null;
        if ($level->name == 'Creator' || $level->name == 'Influencer') {
            $nextPaymentDate = Carbon::now()->addDays(30); //->format('Y-m-d H:i:s');
        } else {
            $nextPaymentDate = Carbon::now()->addDays(365);
        }

        UserLevel::updateOrCreate(
            [
                'user_id' => auth()->id(),

            ],
            [
                'level_id' => $level->id,
                'plan_name' => $level->name,
                'plan_code' => null,
                'subscription_code' => null,
                'email_token' => null,
                'start_date' => now(),
                'status' => 'active',
                'next_payment_date' => $nextPaymentDate
            ]
        );

        Mail::to($user->email)->send(new AccessCodeMail($code, $user));
        return $code;
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccessCodeMail;
use App\Models\AccessCode;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AccessCodeController extends Controller
{
    public function sendAccessCode(){
        $res = securityVerification();
        if($res == 'OK'){
            $level = Level::all();
            return view('admin.accesscode.send_code', ['levels' => $level]);
        }
    }

    public function processValidateCode(Request $request){

        
        $res = securityVerification();
        if($res === 'OK'){
            
            if($request->validationCode == 'LONZETY'){
                $code = $this->generateCode(7);
            
                $ref = time();
                $level = Level::where('id', $request->level)->first();

                $chekIfNotRedeemed = AccessCode::where('email', $request->email)->where('is_active', true)->first();
                
                if($chekIfNotRedeemed){
                    return back()->with('error', 'Email address has active access code');
                }

                AccessCode::create(['tx_id' => $ref,'name' =>$level->name, 'email' => $request->email, 'amount' => $level->amount, 'code' => $code, 'level_id' => $level->id]);
                // Mail::to($request->email)->send(new AccessCodeMail($code));

                return back()->with('success', 'Access Code Sent');

            }else{
                return 'no show';
            }


        }else{
            return redirect('error');
        }
    }

    public function generateCode($number){
        $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $code='';

        for($i=0;$i<$number;$i++){
           $code .= $alph[rand(0, 35)];
        }

        return $code;
    }


    public function listAccessCode(){
        $list = AccessCode::orderBy('created_at', 'DESC')->get();

        return view('admin.accesscode.list', ['lists' => $list]);
    }


}

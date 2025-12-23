<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LevelPlanId;
use Illuminate\Http\Request;

class LevelManagementController extends Controller
{
    public function index()
    {
        $levels = Level::orderBy('name', 'asc')->get();
        return view('admin.level_management.index', ['levels' => $levels]);
    }

    public  function generatePaystackPlanId($levelId)
    {

        $level = Level::find($levelId);

        if (!$level) {
            abort('Level is not Valid');
        }

        $chek = LevelPlanId::where('level_id', $level->id)->where('status', 'active')->first();

        if (!$chek) {
            //convert currency to Naira

            $convertedAmount = convertToBaseCurrency($level->amount, 'NGN');

            $creatPlan = createPlan($level->name, $convertedAmount);


            LevelPlanId::create([
                'level_id' => $level->id,
                'level_name' => $level->name,
                'provider' => 'Paystack',
                'plan_id' => $creatPlan['data']['plan_code'],
                'amount' => $convertedAmount,
                'currency' => 'NGN',
                'status' => 'active'
            ]);

            return back()->with('success', 'Plan created Successfully');
            
        } else {
            return back()->with('error', 'Plan Already Exist, you can only update it');
        }
    }
}

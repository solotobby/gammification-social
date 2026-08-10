<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LevelPlanId;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;

class LevelManagementController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function index()
    {
        $levels = Level::query()->with('planId')->orderBy('name', 'asc')->get();

        return view('admin.level_management.index', ['levels' => $levels]);
    }

    public function generatePaystackPlanId(Level $level)
    {

        $existingPlan = LevelPlanId::query()
            ->where('level_id', $level->id)
            ->where('status', 'active')
            ->first();

        if ($existingPlan) {
            return back()->with('error', 'Plan already exists. You can only update it.');
        }

        $convertedAmount = convertToBaseCurrency($level->amount, 'NGN');
        $creatPlan = createPlan($level->name, $convertedAmount);

        $plan = LevelPlanId::create([
            'level_id' => $level->id,
            'level_name' => $level->name,
            'provider' => 'Paystack',
            'plan_code' => $creatPlan['data']['plan_code'],
            'amount' => $convertedAmount,
            'currency' => 'NGN',
            'status' => 'active',
        ]);

        $this->audit->log('level.paystack_plan_created', $level, [
            'plan_code' => $plan->plan_code,
        ]);

        return back()->with('success', 'Plan created successfully.');
    }
}

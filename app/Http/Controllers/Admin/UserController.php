<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawalMethod;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected AdminUserService $users) {}

    public function userList(Request $request)
    {
        $level = $request->query('level', 'all');

        return view('admin.user.userlist', [
            'users' => $this->users->listUsers($level),
            'level' => $level,
            'levelTabs' => Level::query()->orderBy('name')->pluck('name', 'name'),
        ]);
    }

    public function userSearch(Request $request)
    {
        $query = trim($request->input('query'));

        if (! $query) {
            return redirect()->route('admin.users.index')->with('error', 'Please enter a search term.');
        }

        return view('admin.user.search-result', [
            'users' => $this->users->searchUsers($query),
            'query' => $query,
        ]);
    }

    public function userInfo(User $user)
    {
        return view('admin.user.user_info', $this->users->profileData($user->id));
    }

    public function updateCurrency(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'currency' => 'required|string|max:10',
        ]);

        $wallet = $this->users->updateCurrency($validated['user_id'], $validated['currency']);

        return back()->with('success', 'Account currency changed to ' . $wallet->currency);
    }

    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'status' => 'required|string',
        ]);

        $user = $this->users->changeStatus($validated['user_id'], $validated['status']);

        return back()->with('success', 'Account status changed to ' . $user->status);
    }

    public function upgradeProcess(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'level' => 'required',
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->with('error', 'Invalid validation code.');
        }

        $user = User::query()->with('wallet')->findOrFail($validated['user_id']);
        $level = Level::query()->findOrFail($validated['level']);

        $this->users->upgradeUser($user, $level);

        return back()->with('success', 'Upgrade successful: ' . $level->name);
    }

    public function creditBonus(User $user, string $level)
    {
        if (! in_array($level, ['Creator', 'Influencer'], true)) {
            return back()->with('error', 'Upgrade bonus is allowed for Creator and Influencer only.');
        }

        $this->users->creditBonus($user->id, $level);

        return back()->with('success', 'Upgrade bonus added for ' . $level);
    }

    public function processWalletCredit(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'bank_code' => 'required|numeric',
            'account_number' => 'required|numeric',
            'amount' => 'required|numeric|min:1',
            'validationCode' => 'required|string',
        ]);

        if ($validated['validationCode'] !== config('services.env.validation_code')) {
            return back()->with('error', 'Invalid validation code');
        }

        try {
            $this->users->transferWallet(
                User::findOrFail($validated['user_id']),
                (float) $validated['amount'],
                $validated['bank_code'],
                $validated['account_number']
            );

            return back()->with('success', 'Transfer successful');
        } catch (\Throwable $e) {
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    public function postList(User $user)
    {
        return view('admin.user.posts', [
            'user' => $user,
            'posts' => Post::query()
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(50),
        ]);
    }

    public function transactionList(User $user)
    {
        return view('admin.user.transactions', [
            'user' => $user,
            'transactions' => Transaction::query()
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(50),
        ]);
    }

    public function bankInformation()
    {
        $withdrawals = WithdrawalMethod::query()
            ->with('user:id,name,email')
            ->latest()
            ->paginate(50);

        return view('admin.user.bank_info', compact('withdrawals'));
    }
}

<?php

namespace App\Livewire\User;

use App\Models\Profile;
use App\Models\Social;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class Settings extends Component
{
    #[Url(history: true, as: 'tab')]
    public string $activeTab = 'profile';

    public string $facebook = '';
    public string $twitter = '';
    public string $instagram = '';
    public string $tiktok = '';
    public string $pinterest = '';
    public string $linkedin = '';

    public string $username = '';
    public string $about = '';
    public ?string $date_of_birth = null;
    public string $gender = '';
    public string $location = '';

    public bool $canEditUsername = false;
    public ?string $usernameNextEditDate = null;

    public string $userName = '';
    public string $userEmail = '';
    public string $userLevel = '';
    public string $referralCode = '';
    public string $profileUrl = '';

    protected function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'You must be at least 13 years old to use Payhankey.',
        ];
    }

    public function mount(): void
    {
        $user = Auth::user()->load(['social', 'profile']);

        $social = $user->social;
        $profile = $user->profile;

        $this->facebook = (string) ($social?->facebook ?? '');
        $this->twitter = (string) ($social?->twitter ?? '');
        $this->instagram = (string) ($social?->instagram ?? '');
        $this->linkedin = (string) ($social?->linkedin ?? '');
        $this->pinterest = (string) ($social?->pinterest ?? '');
        $this->tiktok = (string) ($social?->tiktok ?? '');

        $this->username = $user->username;
        $this->about = (string) ($profile?->about ?? '');
        $this->date_of_birth = $profile?->date_of_birth;
        $this->gender = (string) ($profile?->gender ?? '');
        $this->location = (string) ($profile?->location ?? '');

        $lastUsernameChange = $profile?->username_updated_at;
        $this->canEditUsername = ! $lastUsernameChange || $lastUsernameChange->copy()->addMonths(6)->isPast();
        $this->usernameNextEditDate = $lastUsernameChange
            ? $lastUsernameChange->copy()->addMonths(6)->toFormattedDateString()
            : null;

        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userLevel = userLevel($user->id);
        $this->referralCode = (string) $user->referral_code;
        $this->profileUrl = url('profile/' . $user->username);

        if (! in_array($this->activeTab, ['profile', 'social'], true)) {
            $this->activeTab = 'profile';
        }
    }

    public function switchTab(string $tab): void
    {
        if (! in_array($tab, ['profile', 'social'], true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    protected function profileRules(): array
    {
        return [
            'username' => 'required|string|min:3|max:20|alpha_dash|unique:users,username,' . Auth::id(),
            'about' => 'nullable|string|max:40',
            'gender' => 'nullable|in:male,female',
            'location' => 'nullable|string|max:50',
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:' . now()->subYears(13)->toDateString()],
        ];
    }

    protected function socialRules(): array
    {
        $handle = 'nullable|string|max:100';

        return [
            'facebook' => $handle,
            'twitter' => $handle,
            'instagram' => $handle,
            'tiktok' => $handle,
            'linkedin' => $handle,
            'pinterest' => $handle,
        ];
    }

    public function updateProfile(): void
    {
        $user = Auth::user();
        $usernameChanged = $this->username !== $user->username;

        if ($usernameChanged && ! $this->canEditUsername) {
            $this->addError('username', 'Username can only be changed once every 6 months.');
            return;
        }

        $this->validate($this->profileRules());

        if ($usernameChanged) {
            $user->update(['username' => $this->username]);
        }

        $profileData = [
            'about' => $this->about !== '' ? $this->about : null,
            'date_of_birth' => $this->date_of_birth ?: null,
            'gender' => $this->gender !== '' ? $this->gender : null,
            'location' => $this->location !== '' ? $this->location : null,
        ];

        if ($usernameChanged) {
            $profileData['username_updated_at'] = now();
            $this->canEditUsername = false;
            $this->usernameNextEditDate = now()->addMonths(6)->toFormattedDateString();
            $this->profileUrl = url('profile/' . $this->username);
        }

        Profile::updateOrCreate(['user_id' => $user->id], $profileData);

        session()->flash('settings_success', 'Profile updated successfully.');
    }

    public function updateSocial(): void
    {
        $this->validate($this->socialRules());

        Social::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'facebook' => $this->facebook !== '' ? $this->facebook : null,
                'instagram' => $this->instagram !== '' ? $this->instagram : null,
                'twitter' => $this->twitter !== '' ? $this->twitter : null,
                'tiktok' => $this->tiktok !== '' ? $this->tiktok : null,
                'linkedin' => $this->linkedin !== '' ? $this->linkedin : null,
                'pinterest' => $this->pinterest !== '' ? $this->pinterest : null,
            ],
        );

        session()->flash('settings_success', 'Social links updated successfully.');
    }

    public function render()
    {
        return view('livewire.user.settings');
    }
}

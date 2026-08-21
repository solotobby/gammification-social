<?php

namespace App\Livewire\User;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;

class FeedbackForm extends Component
{
    public string $type = 'suggestion';

    public string $subject = '';

    public string $message = '';

    public function submit(): void
    {
        $userId = Auth::id();
        $key = 'feedback-submit:'.$userId;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('message', 'Please wait '.$seconds.' seconds before sending more feedback.');

            return;
        }

        $validated = $this->validate([
            'type' => ['required', Rule::in(array_keys(Feedback::TYPES))],
            'subject' => ['required', 'string', 'min:4', 'max:120'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        $feedback = Feedback::create([
            'user_id' => $userId,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
            'last_message_at' => now(),
            'last_message_by' => 'user',
        ]);

        $feedback->messages()->create([
            'user_id' => $userId,
            'body' => $validated['message'],
            'is_staff' => false,
        ]);

        RateLimiter::hit($key, 60);

        $this->reset(['subject', 'message']);
        $this->type = 'suggestion';

        $this->redirect(route('feedback.show', $feedback), navigate: true);
    }

    public function render()
    {
        $recent = Feedback::query()
            ->where('user_id', Auth::id())
            ->withCount('messages')
            ->latest('last_message_at')
            ->latest()
            ->limit(12)
            ->get();

        return view('livewire.user.feedback', [
            'types' => Feedback::TYPES,
            'recent' => $recent,
        ]);
    }
}

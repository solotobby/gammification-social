<?php

namespace App\Livewire\User;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class FeedbackThread extends Component
{
    public Feedback $feedback;

    public string $reply = '';

    public function mount(Feedback $feedback): void
    {
        abort_unless($feedback->user_id === Auth::id(), 403);
        $this->feedback = $feedback;
    }

    public function sendReply(): void
    {
        abort_unless($this->feedback->user_id === Auth::id(), 403);

        if (! $this->feedback->isOpen()) {
            $this->addError('reply', 'This conversation is closed.');

            return;
        }

        $key = 'feedback-reply:'.Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('reply', 'Please wait a moment before sending another reply.');

            return;
        }

        $validated = $this->validate([
            'reply' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $this->feedback->addMessage($validated['reply'], Auth::id(), false);
        RateLimiter::hit($key, 60);

        $this->reply = '';
        $this->feedback->refresh();
        $this->feedback->load(['messages.user:id,name,username,avatar']);

        session()->flash('success', 'Reply sent.');
    }

    public function render()
    {
        $this->feedback->load(['messages.user:id,name,username,avatar']);

        return view('livewire.user.feedback-thread', [
            'messages' => $this->feedback->messages,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $type = (string) $request->get('type', '');
        $status = (string) $request->get('status', '');

        $base = Feedback::query();

        $stats = [
            'total' => (clone $base)->count(),
            'new' => (clone $base)->where('status', 'new')->count(),
            'reviewed' => (clone $base)->where('status', 'reviewed')->count(),
            'awaiting' => (clone $base)->where('last_message_by', 'user')->whereNotIn('status', ['closed'])->count(),
        ];

        $items = Feedback::query()
            ->with(['user:id,name,username,email,avatar'])
            ->withCount('messages')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($type !== '' && array_key_exists($type, Feedback::TYPES), fn ($q) => $q->where('type', $type))
            ->when($status !== '' && array_key_exists($status, Feedback::STATUSES), fn ($q) => $q->where('status', $status))
            ->orderByRaw("CASE WHEN last_message_by = 'user' AND status != 'closed' THEN 0 ELSE 1 END")
            ->latest('last_message_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.feedback.index', [
            'items' => $items,
            'stats' => $stats,
            'search' => $search,
            'type' => $type,
            'status' => $status,
            'types' => Feedback::TYPES,
            'statuses' => Feedback::STATUSES,
        ]);
    }

    public function show(Feedback $feedback)
    {
        $feedback->load([
            'user:id,name,username,email,avatar,phone',
            'reviewer:id,name,username',
            'messages.user:id,name,username,avatar',
        ]);

        return view('admin.feedback.show', [
            'feedback' => $feedback,
            'types' => Feedback::TYPES,
            'statuses' => Feedback::STATUSES,
        ]);
    }

    public function reply(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        if (! $feedback->isOpen()) {
            return back()->with('error', 'This conversation is closed. Re-open it before replying.');
        }

        $feedback->addMessage($data['body'], Auth::id(), true);

        $this->audit->log('feedback.replied', $feedback);

        return back()->with('success', 'Reply sent to the user.');
    }

    public function update(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Feedback::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $feedback->update([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->audit->log('feedback.updated', $feedback, [
            'status' => $feedback->status,
        ]);

        return back()->with('success', 'Feedback updated.');
    }
}

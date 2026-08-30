<?php

namespace App\Services\Admin;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationMessageAttachment;
use App\Models\User;
use App\Models\UserBlock;
use App\Services\MessageService;
use App\Support\AdminDateRange;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminMessagingService
{
    public function __construct(private MessageService $messages) {}

    public function managementStats(AdminDateRange $dateRange): array
    {
        $rangeMessages = ConversationMessage::query()
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end]);

        $messagesInRange = (clone $rangeMessages)->count();

        $attachmentsInRange = ConversationMessageAttachment::query()
            ->whereHas(
                'message',
                fn ($q) => $q->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            )
            ->count();

        return [
            'total_conversations' => Conversation::count(),
            'total_messages' => ConversationMessage::count(),
            'total_attachments' => ConversationMessageAttachment::count(),
            'total_blocks' => UserBlock::count(),
            'messages_in_range' => $messagesInRange,
            'text_in_range' => (clone $rangeMessages)->where('type', 'text')->count(),
            'images_in_range' => (clone $rangeMessages)->where('type', 'image')->count(),
            'attachments_in_range' => $attachmentsInRange,
            'conversations_active_in_range' => Conversation::query()
                ->whereBetween('last_message_at', [$dateRange->start, $dateRange->end])
                ->count(),
            'unique_senders_in_range' => (int) (clone $rangeMessages)
                ->selectRaw('COUNT(DISTINCT user_id) as aggregate')
                ->value('aggregate'),
            'avg_messages_per_day' => round($messagesInRange / max(1, $dateRange->days()), 1),
            'volume_chart' => $this->volumeChart($dateRange),
            'by_type' => (clone $rangeMessages)
                ->selectRaw('type, COUNT(*) as total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->get(),
            'top_messengers' => $this->topMessengers($dateRange, 10),
            'recent_conversations' => $this->recentConversations(8),
            'recent_messages' => ConversationMessage::query()
                ->with(['user:id,name,username,avatar', 'conversation:id,last_message_at'])
                ->latest()
                ->limit(8)
                ->get(),
        ];
    }

    /**
     * @return array{labels: array<int, string>, messages: array<int, int>, images: array<int, int>}
     */
    public function volumeChart(AdminDateRange $dateRange): array
    {
        $days = min($dateRange->days(), 60);
        $start = $dateRange->end->copy()->subDays($days - 1)->startOfDay();

        $labels = [];
        $messages = [];
        $images = [];

        $rows = ConversationMessage::query()
            ->whereBetween('created_at', [$start, $dateRange->end])
            ->selectRaw('DATE(created_at) as day, type, COUNT(*) as total')
            ->groupBy('day', 'type')
            ->orderBy('day')
            ->get()
            ->groupBy('day');

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i)->toDateString();
            $labels[] = $start->copy()->addDays($i)->format('M j');
            $dayRows = $rows->get($day, collect());
            $messages[] = (int) ($dayRows->firstWhere('type', 'text')?->total ?? 0)
                + (int) ($dayRows->firstWhere('type', 'image')?->total ?? 0);
            $images[] = (int) ($dayRows->firstWhere('type', 'image')?->total ?? 0);
        }

        return compact('labels', 'messages', 'images');
    }

    public function topMessengers(AdminDateRange $dateRange, int $limit = 10): Collection
    {
        return User::query()
            ->select('users.id', 'users.name', 'users.username', 'users.email', 'users.avatar', 'users.status')
            ->selectSub(
                ConversationMessage::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('conversation_messages.user_id', 'users.id')
                    ->whereBetween('created_at', [$dateRange->start, $dateRange->end]),
                'messages_count'
            )
            ->whereExists(function ($q) use ($dateRange) {
                $q->select(DB::raw(1))
                    ->from('conversation_messages')
                    ->whereColumn('conversation_messages.user_id', 'users.id')
                    ->whereBetween('conversation_messages.created_at', [$dateRange->start, $dateRange->end]);
            })
            ->orderByDesc('messages_count')
            ->limit($limit)
            ->get();
    }

    public function recentConversations(int $limit = 8): Collection
    {
        return Conversation::query()
            ->with([
                'participants.user:id,name,username,avatar,status',
                'messages' => fn ($q) => $q->latest()->limit(1)->with('attachments'),
            ])
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->limit($limit)
            ->get();
    }

    public function searchConversations(
        AdminDateRange $dateRange,
        ?string $search = null,
    ): LengthAwarePaginator {
        $query = Conversation::query()
            ->with([
                'participants.user:id,name,username,email,avatar,status',
                'messages' => fn ($q) => $q->latest()->limit(1)->with('attachments'),
            ])
            ->withCount('messages')
            ->where(function ($q) use ($dateRange) {
                $q->whereBetween('last_message_at', [$dateRange->start, $dateRange->end])
                    ->orWhereHas(
                        'messages',
                        fn ($m) => $m->whereBetween('created_at', [$dateRange->start, $dateRange->end])
                    );
            })
            ->orderByDesc('last_message_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('participants.user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(25)->withQueryString();
    }

    public function searchMessages(
        AdminDateRange $dateRange,
        ?string $search = null,
        ?string $type = null,
    ): LengthAwarePaginator {
        $query = ConversationMessage::query()
            ->with([
                'user:id,name,username,avatar',
                'conversation.participants.user:id,name,username',
                'attachments',
            ])
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest();

        if ($type && in_array($type, ['text', 'image'], true)) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('body', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhere('conversation_id', $search)
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(30)->withQueryString();
    }

    public function searchMedia(
        AdminDateRange $dateRange,
        ?string $search = null,
    ): LengthAwarePaginator {
        $query = ConversationMessageAttachment::query()
            ->with([
                'message.user:id,name,username,avatar',
                'message.conversation.participants.user:id,name,username',
            ])
            ->whereHas(
                'message',
                fn ($q) => $q->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            )
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('path', 'like', "%{$search}%")
                    ->orWhere('message_id', $search)
                    ->orWhereHas('message.conversation', fn ($c) => $c->where('id', $search))
                    ->orWhereHas('message.user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(36)->withQueryString();
    }

    public function conversationDetail(Conversation $conversation): array
    {
        $conversation->load([
            'participants.user:id,name,username,email,avatar,status',
        ]);

        $messages = $conversation->messages()
            ->with(['user:id,name,username,avatar', 'attachments'])
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return [
            'conversation' => $conversation,
            'messages' => $messages,
            'message_count' => $conversation->messages()->count(),
            'attachment_count' => ConversationMessageAttachment::query()
                ->whereHas('message', fn ($q) => $q->where('conversation_id', $conversation->id))
                ->count(),
        ];
    }

    public function participantLabel(Conversation $conversation): string
    {
        $names = $conversation->participants
            ->map(fn ($p) => $p->user ? '@'.$p->user->username : 'Unknown')
            ->filter()
            ->values();

        return $names->isNotEmpty() ? $names->join(' ↔ ') : 'No participants';
    }

    public function previewMessage(?ConversationMessage $message): string
    {
        if (! $message) {
            return 'No messages yet';
        }

        return $this->messages->previewText($message, $message->user_id ?? '');
    }

    public function typeLabel(string $type): string
    {
        return match ($type) {
            'image' => 'Image',
            'text' => 'Text',
            default => Str::title($type),
        };
    }
}

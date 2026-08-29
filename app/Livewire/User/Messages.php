<?php

namespace App\Livewire\User;

use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Support\SafeBroadcaster;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class Messages extends Component
{
    use WithFileUploads;

    #[Url(as: 'c', history: true)]
    public ?string $activeId = null;

    public string $listFilter = 'all';

    public string $search = '';

    public string $draft = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $uploads = [];

    public bool $showNewModal = false;

    public string $newUserQuery = '';

    /** @var array<int, string> */
    public array $onlineUserIds = [];

    /** @var array<string, bool> */
    public array $typingByConversation = [];

    public function mount(?string $conversation = null): void
    {
        $startUsername = request()->string('start')->trim()->toString();
        if ($startUsername !== '') {
            $target = User::query()->where('username', $startUsername)->first();

            if (! $target) {
                session()->flash('error', 'User not found.');

                return;
            }

            $created = app(ConversationService::class)->findOrCreateDirect(Auth::user(), $target);
            $this->redirect(route('messages.show', $created->id), navigate: true);

            return;
        }

        if ($conversation) {
            app(ConversationService::class)->assertParticipant($conversation, Auth::id());
            $this->activeId = $conversation;
            $this->markActiveRead(broadcast: false);

            return;
        }

        if ($this->activeId) {
            $participant = app(ConversationService::class)->participant($this->activeId, Auth::id());
            if (! $participant || $participant->hidden_at) {
                $this->activeId = null;
            } else {
                $this->markActiveRead(broadcast: false);
            }
        }
    }

    public function selectConversation(string $id): void
    {
        app(ConversationService::class)->assertParticipant($id, Auth::id());
        $this->activeId = $id;
        $this->draft = '';
        $this->uploads = [];
        $this->typingByConversation[$id] = false;
        $this->markActiveRead();
    }

    public function setFilter(string $filter): void
    {
        $this->listFilter = in_array($filter, ['all', 'unread'], true) ? $filter : 'all';
    }

    public function openNewModal(): void
    {
        $this->showNewModal = true;
        $this->newUserQuery = '';
    }

    public function closeNewModal(): void
    {
        $this->showNewModal = false;
        $this->newUserQuery = '';
    }

    public function startConversation(string $username): void
    {
        $target = User::query()->where('username', $username)->first();

        if (! $target) {
            $this->addError('newUserQuery', 'User not found.');

            return;
        }

        $conversation = app(ConversationService::class)->findOrCreateDirect(Auth::user(), $target);

        $this->showNewModal = false;
        $this->redirect(route('messages.show', $conversation->id), navigate: true);
    }

    public function removeUpload(int $index): void
    {
        unset($this->uploads[$index]);
        $this->uploads = array_values($this->uploads);
    }

    public function sendMessage(): void
    {
        if (! $this->activeId) {
            return;
        }

        $key = 'message-send:'.Auth::id();
        if (RateLimiter::tooManyAttempts($key, 30)) {
            $this->addError('draft', 'You are sending messages too quickly. Please wait a moment.');

            return;
        }

        $this->validate([
            'draft' => ['nullable', 'string', 'max:5000'],
            'uploads' => ['array', 'max:4'],
            'uploads.*' => ['image', 'max:'.(int) config('media.image_max_kb', 10240)],
        ]);

        $conversation = Conversation::query()->findOrFail($this->activeId);
        $message = app(MessageService::class)->send(
            $conversation,
            Auth::user(),
            $this->draft,
            $this->uploads,
        );

        RateLimiter::hit($key, 60);

        $this->draft = '';
        $this->uploads = [];
        $this->dispatch('message-thread-scroll');
        $this->dispatch('stop-typing');

        $this->dispatch('message-appended', message: $message->toBroadcastArray());
    }

    public function broadcastTyping(bool $typing = true): void
    {
        if (! $this->activeId) {
            return;
        }

        SafeBroadcaster::emit(new UserTyping(
            $this->activeId,
            Auth::id(),
            Auth::user()->name,
            $typing,
        ), toOthers: true);
    }

    public function togglePin(): void
    {
        if (! $this->activeId) {
            return;
        }

        $participant = app(ConversationService::class)->assertParticipant($this->activeId, Auth::id());
        app(ConversationService::class)->togglePin($participant);
    }

    public function toggleMute(): void
    {
        if (! $this->activeId) {
            return;
        }

        $participant = app(ConversationService::class)->assertParticipant($this->activeId, Auth::id());
        app(ConversationService::class)->toggleMute($participant);
    }

    public function deleteChat(): void
    {
        if (! $this->activeId) {
            return;
        }

        $participant = app(ConversationService::class)->assertParticipant($this->activeId, Auth::id());
        app(ConversationService::class)->hide($participant);
        $this->activeId = null;
    }

    public function blockActiveUser(): void
    {
        if (! $this->activeId) {
            return;
        }

        $conversation = Conversation::query()->findOrFail($this->activeId);
        $other = app(ConversationService::class)->otherParticipant($conversation, Auth::id());

        if (! $other) {
            return;
        }

        $service = app(ConversationService::class);

        if ($service->isBlockedBy(Auth::user(), $other)) {
            $service->unblockUser(Auth::user(), $other);

            return;
        }

        $service->blockUser(Auth::user(), $other);
        $this->activeId = null;
    }

    #[On('echo:message.sent')]
    public function onMessageSent($message = null): void
    {
        if (! is_array($message)) {
            return;
        }

        if (($message['conversation_id'] ?? null) !== $this->activeId) {
            return;
        }

        if (($message['user_id'] ?? null) === Auth::id()) {
            return;
        }

        $this->markActiveRead();
        $this->dispatch('message-thread-scroll');
    }

    #[On('echo:messages.read')]
    public function onMessagesRead($conversation_id = null, $user_id = null): void
    {
        if ($conversation_id !== $this->activeId) {
            return;
        }
    }

    #[On('echo:user.typing')]
    public function onUserTyping($conversation_id = null, $user_id = null, $typing = false): void
    {
        if (! $conversation_id || $user_id === Auth::id()) {
            return;
        }

        $this->typingByConversation[$conversation_id] = (bool) $typing;
    }

    #[On('echo:conversation.updated')]
    public function onConversationUpdated(): void
    {
        // Re-render list via Livewire refresh.
    }

    #[On('presence:online-users')]
    public function onPresenceUpdated($userIds = []): void
    {
        $this->onlineUserIds = is_array($userIds) ? $userIds : [];
    }

    protected function markActiveRead(bool $broadcast = true): void
    {
        if (! $this->activeId) {
            return;
        }

        $conversation = Conversation::query()->find($this->activeId);
        if ($conversation) {
            app(MessageService::class)->markConversationRead($conversation, Auth::user(), $broadcast);
        }
    }

    public function render()
    {
        $userId = Auth::id();
        $conversationService = app(ConversationService::class);
        $messageService = app(MessageService::class);

        $participantRows = ConversationParticipant::query()
            ->where('user_id', $userId)
            ->whereNull('hidden_at')
            ->with(['conversation'])
            ->get()
            ->sortByDesc(fn ($row) => $row->conversation?->last_message_at?->timestamp ?? 0)
            ->values();

        $conversations = $participantRows
            ->map(function (ConversationParticipant $row) use ($conversationService, $messageService, $userId) {
                $conversation = $row->conversation;
                if (! $conversation) {
                    return null;
                }

                $item = $messageService->conversationListItem($conversation, $userId);
                $other = $conversationService->otherParticipant($conversation, $userId);
                $item['online'] = $other && in_array($other->id, $this->onlineUserIds, true);
                $item['typing'] = (bool) ($this->typingByConversation[$conversation->id] ?? false);

                if ($row->pinned_at) {
                    $item['pinned'] = true;
                }

                return $item;
            })
            ->filter()
            ->sortByDesc(fn ($item) => ($item['pinned'] ?? false) ? PHP_INT_MAX : 0)
            ->values();

        $q = mb_strtolower(trim($this->search));
        $conversations = $conversations->filter(function (array $c) use ($q) {
            if ($this->listFilter === 'unread' && (int) ($c['unread'] ?? 0) < 1) {
                return false;
            }

            if ($q === '') {
                return true;
            }

            return str_contains(mb_strtolower($c['name']), $q)
                || str_contains(mb_strtolower($c['username']), $q);
        })->values()->all();

        $thread = $this->buildThread($messageService, $conversationService);

        $searchUsers = [];
        if ($this->showNewModal && strlen(trim($this->newUserQuery)) >= 2) {
            $needle = trim($this->newUserQuery);
            $searchUsers = User::query()
                ->where('id', '!=', $userId)
                ->where(function ($query) use ($needle) {
                    $query->where('username', 'like', '%'.$needle.'%')
                        ->orWhere('name', 'like', '%'.$needle.'%');
                })
                ->limit(8)
                ->get(['id', 'name', 'username', 'avatar'])
                ->all();
        }

        return view('livewire.user.messages', [
            'conversations' => $conversations,
            'thread' => $thread,
            'searchUsers' => $searchUsers,
            'me' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'avatar' => Auth::user()->avatar ?: asset('src/assets/media/avatars/avatar13.jpg'),
            ],
        ])->layout('layouts.app');
    }

    protected function buildThread(MessageService $messageService, ConversationService $conversationService): array
    {
        if (! $this->activeId) {
            return [
                'meta' => null,
                'messages' => [],
            ];
        }

        $conversation = Conversation::query()->find($this->activeId);
        if (! $conversation) {
            return ['meta' => null, 'messages' => []];
        }

        $other = $conversationService->otherParticipant($conversation, Auth::id());
        $participant = $conversationService->participant($conversation->id, Auth::id());

        return [
            'meta' => [
                'id' => $conversation->id,
                'name' => $other?->name ?? 'Unknown',
                'username' => $other?->username ?? 'user',
                'avatar' => $other?->avatar ?: asset('src/assets/media/avatars/avatar13.jpg'),
                'online' => $other && in_array($other->id, $this->onlineUserIds, true),
                'typing' => (bool) ($this->typingByConversation[$conversation->id] ?? false),
                'pinned' => (bool) $participant?->pinned_at,
                'muted' => (bool) $participant?->muted_at,
                'blocked' => $other && $conversationService->isBlockedBy(Auth::user(), $other),
            ],
            'messages' => $messageService->threadMessages($conversation, Auth::user()),
        ];
    }
}

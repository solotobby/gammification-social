<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\Admin\AdminMessagingService;
use App\Support\AdminDateRange;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function __construct(private AdminMessagingService $messaging) {}

    public function index(Request $request)
    {
        $dateRange = AdminDateRange::fromRequest($request);
        $search = $request->string('q')->trim()->toString() ?: null;
        $type = $request->string('type')->trim()->toString() ?: null;
        $tab = $request->string('tab')->trim()->toString() ?: 'overview';

        if (! in_array($tab, ['overview', 'conversations', 'messages', 'media'], true)) {
            $tab = 'overview';
        }

        return view('admin.messaging.index', [
            'dateRange' => $dateRange,
            'tab' => $tab,
            'stats' => $this->messaging->managementStats($dateRange),
            'conversations' => $tab === 'conversations'
                ? $this->messaging->searchConversations($dateRange, $search)
                : null,
            'messages' => $tab === 'messages'
                ? $this->messaging->searchMessages($dateRange, $search, $type)
                : null,
            'media' => $tab === 'media'
                ? $this->messaging->searchMedia($dateRange, $search)
                : null,
            'search' => $search ?? '',
            'type' => $type ?? '',
            'messaging' => $this->messaging,
        ]);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $detail = $this->messaging->conversationDetail($conversation);

        return view('admin.messaging.show', array_merge($detail, [
            'messaging' => $this->messaging,
        ]));
    }
}

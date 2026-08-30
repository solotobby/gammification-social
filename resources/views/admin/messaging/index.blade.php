@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-tab-row { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
        .dash-tab-row .dash-tab.is-active { background: var(--dash-accent-soft); border-color: #c7d2fe; color: var(--dash-accent); }
        .dash-tab-row .dash-tab { text-decoration:none; }
        .dash-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; align-items:start; }
        .dash-author { display:flex; align-items:center; gap:.65rem; }
        .dash-author img { width:36px; height:36px; border-radius:50%; object-fit:cover; }
        .dash-badge--text { background: rgba(99,102,241,.12); color:#4338ca; }
        .dash-badge--image { background: rgba(14,165,233,.12); color:#0369a1; }
        .dash-participants { font-size:.8125rem; color:var(--dash-muted); }
        .dash-media-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(140px, 1fr));
            gap:.75rem;
        }
        .dash-media-card {
            border:1px solid var(--dash-border);
            border-radius:12px;
            overflow:hidden;
            background:#fff;
        }
        .dash-media-card img {
            display:block;
            width:100%;
            aspect-ratio:1;
            object-fit:cover;
            background:#f8fafc;
        }
        .dash-media-card__meta {
            padding:.55rem .65rem;
            font-size:.72rem;
            color:var(--dash-muted);
            line-height:1.35;
        }
        .dash-media-card__meta strong {
            display:block;
            color:var(--dash-text);
            font-size:.78rem;
        }
        @media (max-width:960px){ .dash-grid-2{grid-template-columns:1fr;} }
    </style>
@endsection

@section('content')
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Direct messaging</h1>
            <p>Platform DMs — analytics, conversations, and shared media · {{ $dateRange->label() }}</p>
        </div>
    </header>

    @include('admin.partials.date-range-filter', [
        'routeName' => 'admin.messaging.index',
        'extraQuery' => array_filter(['tab' => $tab, 'q' => $search, 'type' => $type]),
    ])

    <div class="dash-tab-row">
        @foreach (['overview' => 'Overview', 'conversations' => 'Conversations', 'messages' => 'Messages', 'media' => 'Media'] as $tabId => $tabLabel)
            <a href="{{ route('admin.messaging.index', array_merge($dateRange->queryParams(), ['tab' => $tabId])) }}"
               class="dash-tab @if($tab === $tabId) is-active @endif">{{ $tabLabel }}</a>
        @endforeach
    </div>

    <section class="dash-section">
        <div class="dash-grid dash-grid--4">
            <div class="dash-kpi">
                <span class="dash-kpi__label">Conversations</span>
                <div class="dash-kpi__value">{{ number_format($stats['total_conversations']) }}</div>
                <div class="dash-muted">{{ number_format($stats['conversations_active_in_range']) }} active in range</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Messages (range)</span>
                <div class="dash-kpi__value">{{ number_format($stats['messages_in_range']) }}</div>
                <div class="dash-muted">~{{ number_format($stats['avg_messages_per_day'], 1) }}/day · {{ number_format($stats['unique_senders_in_range']) }} senders</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">Images (range)</span>
                <div class="dash-kpi__value">{{ number_format($stats['images_in_range']) }}</div>
                <div class="dash-muted">{{ number_format($stats['attachments_in_range']) }} files attached</div>
            </div>
            <div class="dash-kpi">
                <span class="dash-kpi__label">All-time totals</span>
                <div class="dash-kpi__value" style="font-size:1.25rem;line-height:1.3">{{ number_format($stats['total_messages']) }} msgs</div>
                <div class="dash-muted">{{ number_format($stats['total_attachments']) }} media · {{ number_format($stats['total_blocks']) }} blocks</div>
            </div>
        </div>
    </section>

    @if ($tab === 'overview')
        <section class="dash-section dash-grid-2">
            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Message volume</h2>
                    <span class="dash-muted">{{ $dateRange->label() }}</span>
                </div>
                <div class="dash-card__body">
                    <div class="dash-chart" style="height:260px">
                        <canvas id="messaging-volume-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">By message type</h2></div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>Type</th><th>Count</th></tr></thead>
                            <tbody>
                                @forelse ($stats['by_type'] as $row)
                                    <tr>
                                        <td>{{ $messaging->typeLabel($row->type) }}</td>
                                        <td>{{ number_format($row->total) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2"><div class="dash-empty">No messages in this range.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="dash-section dash-grid-2">
            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Top messengers</h2>
                    <span class="dash-muted">{{ $dateRange->label() }}</span>
                </div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>User</th><th>Sent</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($stats['top_messengers'] as $user)
                                    <tr>
                                        <td>
                                            <div class="dash-author">
                                                <img src="{{ $user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                                <div>
                                                    <div style="font-weight:600">{{ displayName($user->name) }}</div>
                                                    <div class="dash-muted" style="font-size:.75rem">{{ '@'.$user->username }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><strong>{{ number_format($user->messages_count) }}</strong></td>
                                        <td><a href="{{ route('admin.users.show', $user) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Profile</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3"><div class="dash-empty">No messaging activity in this range.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Recent conversations</h2>
                    <a href="{{ route('admin.messaging.index', array_merge($dateRange->queryParams(), ['tab' => 'conversations'])) }}" class="dash-link">View all</a>
                </div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>Participants</th><th>Last message</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($stats['recent_conversations'] as $conversation)
                                    @php
                                        $latest = $conversation->messages->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;font-size:.875rem">{{ $messaging->participantLabel($conversation) }}</div>
                                            <div class="dash-muted" style="font-size:.72rem">{{ number_format($conversation->messages_count) }} messages</div>
                                        </td>
                                        <td class="dash-muted" style="font-size:.8125rem">{{ $messaging->previewMessage($latest) }}</td>
                                        <td><a href="{{ route('admin.messaging.show', $conversation) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Open</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3"><div class="dash-empty">No conversations yet.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="dash-section">
            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Recent messages</h2>
                    <a href="{{ route('admin.messaging.index', array_merge($dateRange->queryParams(), ['tab' => 'messages'])) }}" class="dash-link">View all</a>
                </div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>Sender</th><th>Preview</th><th>Type</th><th>When</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($stats['recent_messages'] as $message)
                                    <tr>
                                        <td class="dash-muted">{{ $message->user ? '@'.$message->user->username : '—' }}</td>
                                        <td>{{ $messaging->previewMessage($message) }}</td>
                                        <td><span class="dash-badge dash-badge--{{ $message->type === 'image' ? 'image' : 'text' }}">{{ $messaging->typeLabel($message->type) }}</span></td>
                                        <td class="dash-muted">{{ $message->created_at?->diffForHumans() }}</td>
                                        <td><a href="{{ route('admin.messaging.show', $message->conversation_id) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Thread</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5"><div class="dash-empty">No messages yet.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($tab === 'conversations')
        <section class="dash-section">
            <form method="get" action="{{ route('admin.messaging.index') }}" class="dash-filter-bar" style="margin-bottom:1rem">
                @foreach (array_merge($dateRange->queryParams(), ['tab' => 'conversations']) as $key => $value)
                    @if ($value !== null && $value !== '' && $key !== 'q')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by username, email, or conversation ID" class="dash-input" style="flex:1;min-width:200px">
                <button type="submit" class="dash-btn dash-btn--primary">Search</button>
            </form>

            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Conversations</h2>
                    <span class="dash-muted">Active in {{ $dateRange->label() }}</span>
                </div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Participants</th>
                                    <th>Messages</th>
                                    <th>Last activity</th>
                                    <th>Preview</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($conversations as $conversation)
                                    @php
                                        $latest = $conversation->messages->first();
                                        $hiddenCount = $conversation->participants->whereNotNull('hidden_at')->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;font-size:.875rem">{{ $messaging->participantLabel($conversation) }}</div>
                                            <div class="dash-participants">ID {{ Str::limit($conversation->id, 13) }}</div>
                                            @if ($hiddenCount > 0)
                                                <div class="dash-muted" style="font-size:.72rem">{{ $hiddenCount }} hidden locally</div>
                                            @endif
                                        </td>
                                        <td>{{ number_format($conversation->messages_count) }}</td>
                                        <td class="dash-muted">{{ $conversation->last_message_at?->diffForHumans() ?? '—' }}</td>
                                        <td style="font-size:.8125rem">{{ $messaging->previewMessage($latest) }}</td>
                                        <td><a href="{{ route('admin.messaging.show', $conversation) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Open</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5"><div class="dash-empty">No conversations match this filter.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($conversations->hasPages())
                        <div class="dash-pagination">{{ $conversations->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($tab === 'messages')
        <section class="dash-section">
            <form method="get" action="{{ route('admin.messaging.index') }}" class="dash-filter-bar" style="margin-bottom:1rem;display:flex;flex-wrap:wrap;gap:.5rem;align-items:center">
                @foreach (array_merge($dateRange->queryParams(), ['tab' => 'messages']) as $key => $value)
                    @if ($value !== null && $value !== '' && ! in_array($key, ['q', 'type'], true))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <input type="search" name="q" value="{{ $search }}" placeholder="Search message, sender, conversation ID" class="dash-input" style="flex:1;min-width:180px">
                <select name="type" class="dash-input" style="width:auto">
                    <option value="">All types</option>
                    <option value="text" @selected($type === 'text')>Text</option>
                    <option value="image" @selected($type === 'image')>Image</option>
                </select>
                <button type="submit" class="dash-btn dash-btn--primary">Filter</button>
            </form>

            <div class="dash-card">
                <div class="dash-card__head"><h2 class="dash-card__title">Messages</h2></div>
                <div class="dash-card__body dash-card__body--flush">
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th>Sender</th><th>Content</th><th>Type</th><th>Conversation</th><th>When</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($messages as $message)
                                    <tr>
                                        <td class="dash-muted">{{ $message->user ? '@'.$message->user->username : '—' }}</td>
                                        <td style="max-width:280px">{{ $messaging->previewMessage($message) }}</td>
                                        <td><span class="dash-badge dash-badge--{{ $message->type === 'image' ? 'image' : 'text' }}">{{ $messaging->typeLabel($message->type) }}</span></td>
                                        <td class="dash-muted" style="font-size:.75rem">{{ $message->conversation ? $messaging->participantLabel($message->conversation) : '—' }}</td>
                                        <td class="dash-muted">{{ $message->created_at?->diffForHumans() }}</td>
                                        <td><a href="{{ route('admin.messaging.show', $message->conversation_id) }}" class="dash-btn dash-btn--ghost dash-btn--sm">Thread</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6"><div class="dash-empty">No messages in this range.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($messages->hasPages())
                        <div class="dash-pagination">{{ $messages->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($tab === 'media')
        <section class="dash-section">
            <form method="get" action="{{ route('admin.messaging.index') }}" class="dash-filter-bar" style="margin-bottom:1rem;display:flex;flex-wrap:wrap;gap:.5rem">
                @foreach (array_merge($dateRange->queryParams(), ['tab' => 'media']) as $key => $value)
                    @if ($value !== null && $value !== '' && $key !== 'q')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <input type="search" name="q" value="{{ $search }}" placeholder="Search sender, path, conversation ID" class="dash-input" style="flex:1;min-width:200px">
                <button type="submit" class="dash-btn dash-btn--primary">Search</button>
            </form>

            @if ($media->count())
                <div class="dash-media-grid">
                    @foreach ($media as $attachment)
                        @php
                            $sender = $attachment->message?->user;
                            $conversation = $attachment->message?->conversation;
                        @endphp
                        <a href="{{ route('admin.messaging.show', $conversation) }}" class="dash-media-card" style="text-decoration:none;color:inherit">
                            <img src="{{ $attachment->url() }}" alt="Message media" loading="lazy" onerror="this.src='{{ asset('src/assets/media/photos/photo3.jpg') }}'">
                            <div class="dash-media-card__meta">
                                <strong>{{ $sender ? '@'.$sender->username : 'Unknown' }}</strong>
                                {{ $attachment->message?->created_at?->diffForHumans() ?? '—' }}
                                @if ($conversation)
                                    <div>{{ Str::limit($messaging->participantLabel($conversation), 28) }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
                @if ($media->hasPages())
                    <div class="dash-pagination" style="margin-top:1rem">{{ $media->links('pagination::bootstrap-5') }}</div>
                @endif
            @else
                <div class="dash-card">
                    <div class="dash-card__body"><div class="dash-empty">No shared media in this range.</div></div>
                </div>
            @endif
        </section>
    @endif
</div></div>
@endsection

@section('script')
    @if ($tab === 'overview')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            if (typeof Chart === 'undefined') return;
            var canvas = document.getElementById('messaging-volume-chart');
            if (!canvas) return;

            var labels = @json($stats['volume_chart']['labels'] ?? []);
            var messages = @json($stats['volume_chart']['messages'] ?? []);
            var images = @json($stats['volume_chart']['images'] ?? []);

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'All messages',
                            data: messages,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            borderWidth: 2,
                        },
                        {
                            label: 'Image messages',
                            data: images,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.08)',
                            fill: false,
                            tension: 0.35,
                            pointRadius: 0,
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, font: { size: 11 } },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 11 } } },
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0, font: { size: 11 } } },
                    },
                },
            });
        })();
    </script>
    @endif
@endsection

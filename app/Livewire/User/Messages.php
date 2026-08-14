<?php

namespace App\Livewire\User;

use Livewire\Component;

/**
 * Messaging UI prototype — design only, no persistence / API.
 */
class Messages extends Component
{
    public string $activeId = 'c1';

    public string $listFilter = 'all'; // all | unread

    public string $search = '';

    public string $draft = '';

    /** @var array<int, array<string, mixed>> */
    public array $previewImages = [];

    public function selectConversation(string $id): void
    {
        $this->activeId = $id;
        $this->draft = '';
        $this->previewImages = [];
    }

    public function setFilter(string $filter): void
    {
        $this->listFilter = in_array($filter, ['all', 'unread'], true) ? $filter : 'all';
    }

    public function render()
    {
        return view('livewire.user.messages', [
            'conversations' => $this->mockConversations(),
            'thread' => $this->mockThread($this->activeId),
            'me' => [
                'name' => auth()->user()->name ?? 'You',
                'avatar' => auth()->user()->avatar ?? asset('src/assets/media/avatars/avatar13.jpg'),
            ],
        ])->layout('layouts.app');
    }

    /** @return array<int, array<string, mixed>> */
    private function mockConversations(): array
    {
        $items = [
            [
                'id' => 'c1',
                'name' => 'Amara Okonkwo',
                'username' => 'amaravibes',
                'avatar' => 'https://i.pravatar.cc/150?img=47',
                'level' => 'Influencer',
                'online' => true,
                'typing' => false,
                'unread' => 2,
                'muted' => false,
                'pinned' => true,
                'last_message' => 'The campaign shots look incredible 🔥',
                'last_at' => '2m',
                'last_from_me' => false,
                'last_status' => null,
            ],
            [
                'id' => 'c2',
                'name' => 'Kwame Boateng',
                'username' => 'kwamecreates',
                'avatar' => 'https://i.pravatar.cc/150?img=12',
                'level' => 'Creator',
                'online' => true,
                'typing' => true,
                'unread' => 0,
                'muted' => false,
                'pinned' => false,
                'last_message' => 'Typing…',
                'last_at' => 'Now',
                'last_from_me' => false,
                'last_status' => null,
            ],
            [
                'id' => 'c3',
                'name' => 'Zinhle Mokoena',
                'username' => 'zinhletalks',
                'avatar' => 'https://i.pravatar.cc/150?img=32',
                'level' => 'Creator',
                'online' => false,
                'typing' => false,
                'unread' => 0,
                'muted' => false,
                'pinned' => false,
                'last_message' => 'Sent the payout screenshot',
                'last_at' => '1h',
                'last_from_me' => true,
                'last_status' => 'read',
            ],
            [
                'id' => 'c4',
                'name' => 'Payhankey Support',
                'username' => 'support',
                'avatar' => asset('favicon.png'),
                'level' => 'Basic',
                'online' => true,
                'typing' => false,
                'unread' => 1,
                'muted' => false,
                'pinned' => false,
                'last_message' => 'Your verification is complete.',
                'last_at' => 'Yesterday',
                'last_from_me' => false,
                'last_status' => null,
                'official' => true,
            ],
            [
                'id' => 'c5',
                'name' => 'Tunde Adeyemi',
                'username' => 'tundeshots',
                'avatar' => 'https://i.pravatar.cc/150?img=15',
                'level' => 'Basic',
                'online' => false,
                'typing' => false,
                'unread' => 0,
                'muted' => true,
                'pinned' => false,
                'last_message' => 'Photo',
                'last_at' => 'Mon',
                'last_from_me' => false,
                'last_status' => null,
                'has_image' => true,
            ],
            [
                'id' => 'c6',
                'name' => 'Community: Lagos Creators',
                'username' => 'lagos-creators',
                'avatar' => 'https://i.pravatar.cc/150?img=5',
                'level' => 'Basic',
                'online' => false,
                'typing' => false,
                'unread' => 0,
                'muted' => false,
                'pinned' => false,
                'last_message' => 'Ada: Who’s joining the meetup?',
                'last_at' => 'Sun',
                'last_from_me' => false,
                'last_status' => null,
                'is_group' => true,
            ],
        ];

        $q = mb_strtolower(trim($this->search));

        return array_values(array_filter($items, function (array $c) use ($q) {
            if ($this->listFilter === 'unread' && (int) ($c['unread'] ?? 0) < 1) {
                return false;
            }
            if ($q === '') {
                return true;
            }

            return str_contains(mb_strtolower($c['name']), $q)
                || str_contains(mb_strtolower($c['username']), $q);
        }));
    }

    /** @return array<string, mixed> */
    private function mockThread(string $id): array
    {
        $all = [
            [
                'id' => 'c1',
                'name' => 'Amara Okonkwo',
                'username' => 'amaravibes',
                'avatar' => 'https://i.pravatar.cc/150?img=47',
                'online' => true,
                'typing' => false,
            ],
            [
                'id' => 'c2',
                'name' => 'Kwame Boateng',
                'username' => 'kwamecreates',
                'avatar' => 'https://i.pravatar.cc/150?img=12',
                'online' => true,
                'typing' => true,
            ],
            [
                'id' => 'c3',
                'name' => 'Zinhle Mokoena',
                'username' => 'zinhletalks',
                'avatar' => 'https://i.pravatar.cc/150?img=32',
                'online' => false,
                'typing' => false,
            ],
            [
                'id' => 'c4',
                'name' => 'Payhankey Support',
                'username' => 'support',
                'avatar' => asset('favicon.png'),
                'online' => true,
                'typing' => false,
            ],
            [
                'id' => 'c5',
                'name' => 'Tunde Adeyemi',
                'username' => 'tundeshots',
                'avatar' => 'https://i.pravatar.cc/150?img=15',
                'online' => false,
                'typing' => false,
            ],
            [
                'id' => 'c6',
                'name' => 'Community: Lagos Creators',
                'username' => 'lagos-creators',
                'avatar' => 'https://i.pravatar.cc/150?img=5',
                'online' => false,
                'typing' => false,
            ],
        ];

        $meta = collect($all)->firstWhere('id', $id) ?? $all[0];

        $threads = [
            'c1' => [
                ['id' => 'm1', 'type' => 'date', 'label' => 'Yesterday'],
                [
                    'id' => 'm2',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'Hey! Saw your latest Roll — the lighting was perfect.',
                    'at' => '4:12 PM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm3',
                    'type' => 'text',
                    'mine' => true,
                    'body' => 'Thank you Amara 🙏 I’ve been experimenting with golden hour.',
                    'at' => '4:14 PM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm4',
                    'type' => 'image',
                    'mine' => true,
                    'caption' => 'Behind the scenes from yesterday',
                    'images' => [
                        'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&q=80',
                        'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80',
                    ],
                    'at' => '4:16 PM',
                    'status' => 'read',
                ],
                ['id' => 'm5', 'type' => 'date', 'label' => 'Today'],
                [
                    'id' => 'm6',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'These are beautiful. Want to collab on a paid community challenge next week?',
                    'at' => '9:02 AM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm7',
                    'type' => 'image',
                    'mine' => false,
                    'caption' => null,
                    'images' => [
                        'https://images.unsplash.com/photo-1611162616471-46b635cb1dd2?w=800&q=80',
                    ],
                    'at' => '9:03 AM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm8',
                    'type' => 'text',
                    'mine' => true,
                    'body' => 'I’m in. Let’s keep it simple — short Rolls + weekly theme.',
                    'at' => '9:08 AM',
                    'status' => 'delivered',
                ],
                [
                    'id' => 'm9',
                    'type' => 'text',
                    'mine' => true,
                    'body' => 'I’ll draft a one-pager tonight.',
                    'at' => '9:08 AM',
                    'status' => 'sent',
                ],
                [
                    'id' => 'm10',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'The campaign shots look incredible 🔥',
                    'at' => '9:11 AM',
                    'status' => 'read',
                ],
            ],
            'c2' => [
                ['id' => 'm1', 'type' => 'date', 'label' => 'Today'],
                [
                    'id' => 'm2',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'Can you review the thumbnail before I publish?',
                    'at' => '10:40 AM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm3',
                    'type' => 'image',
                    'mine' => false,
                    'images' => [
                        'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=800&q=80',
                    ],
                    'at' => '10:41 AM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm4',
                    'type' => 'text',
                    'mine' => true,
                    'body' => 'Crop a little tighter on the face — otherwise it’s solid.',
                    'at' => '10:45 AM',
                    'status' => 'read',
                ],
            ],
            'c3' => [
                ['id' => 'm1', 'type' => 'date', 'label' => 'Today'],
                [
                    'id' => 'm2',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'Did your withdrawal go through?',
                    'at' => '8:01 AM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm3',
                    'type' => 'image',
                    'mine' => true,
                    'caption' => 'Yes — here’s the receipt',
                    'images' => [
                        'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&q=80',
                    ],
                    'at' => '8:20 AM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm4',
                    'type' => 'text',
                    'mine' => true,
                    'body' => 'Sent the payout screenshot',
                    'at' => '8:21 AM',
                    'status' => 'read',
                ],
            ],
            'c4' => [
                ['id' => 'm1', 'type' => 'date', 'label' => 'Yesterday'],
                [
                    'id' => 'm2',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'Hi! Thanks for reaching out to Payhankey Support.',
                    'at' => '2:00 PM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm3',
                    'type' => 'text',
                    'mine' => true,
                    'body' => 'I need help verifying my bank details.',
                    'at' => '2:05 PM',
                    'status' => 'read',
                ],
                [
                    'id' => 'm4',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'Your verification is complete.',
                    'at' => '5:40 PM',
                    'status' => 'read',
                ],
            ],
            'c5' => [
                ['id' => 'm1', 'type' => 'date', 'label' => 'Monday'],
                [
                    'id' => 'm2',
                    'type' => 'image',
                    'mine' => false,
                    'images' => [
                        'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=800&q=80',
                        'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=800&q=80',
                        'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=800&q=80',
                        'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=800&q=80',
                    ],
                    'at' => '6:18 PM',
                    'status' => 'read',
                ],
            ],
            'c6' => [
                ['id' => 'm1', 'type' => 'date', 'label' => 'Sunday'],
                [
                    'id' => 'm2',
                    'type' => 'text',
                    'mine' => false,
                    'body' => 'Who’s joining the meetup?',
                    'at' => '3:00 PM',
                    'status' => 'read',
                    'sender' => 'Ada',
                ],
                [
                    'id' => 'm3',
                    'type' => 'text',
                    'mine' => true,
                    'body' => 'I’ll be there around 4.',
                    'at' => '3:12 PM',
                    'status' => 'read',
                ],
            ],
        ];

        return [
            'meta' => $meta,
            'messages' => $threads[$id] ?? $threads['c1'],
        ];
    }
}

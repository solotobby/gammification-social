import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

function messagingConfig() {
    const root = document.getElementById('msg-echo-root');
    if (!root) {
        return null;
    }

    return {
        userId: root.dataset.userId,
        activeConversationId: root.dataset.activeConversationId || '',
        key: root.dataset.reverbKey,
        wsHost: root.dataset.reverbHost,
        wsPort: Number(root.dataset.reverbPort || 8080),
        wssPort: Number(root.dataset.reverbPort || 8080),
        forceTLS: root.dataset.reverbScheme === 'https',
    };
}

function initMessagingEcho() {
    const config = messagingConfig();
    if (!config || !config.key || window.messagingEchoInitialized) {
        return;
    }

    window.messagingEchoInitialized = true;

    window.messagingEcho = new Echo({
        broadcaster: 'reverb',
        key: config.key,
        wsHost: config.wsHost,
        wsPort: config.wsPort,
        wssPort: config.wssPort,
        forceTLS: config.forceTLS,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
        },
    });

    const echo = window.messagingEcho;
    let subscribedConversationId = null;
    let presenceUsers = [];

    const dispatch = (name, payload = {}) => {
        if (window.Livewire) {
            window.Livewire.dispatch(name, payload);
        }
    };

    if (config.userId) {
        echo.private(`user.${config.userId}`)
            .listen('.conversation.updated', () => {
                dispatch('echo:conversation.updated');
            });
    }

    const presence = echo.join('messaging.online');
    const syncPresence = () => {
        dispatch('presence:online-users', {
            userIds: presenceUsers.map((user) => user.id),
        });
    };
    presence.here((users) => {
        presenceUsers = users;
        syncPresence();
    });
    presence.joining((user) => {
        presenceUsers = [...presenceUsers, user];
        syncPresence();
    });
    presence.leaving((user) => {
        presenceUsers = presenceUsers.filter((u) => u.id !== user.id);
        syncPresence();
    });

    window.messagingSubscribeConversation = (conversationId) => {
        if (subscribedConversationId === conversationId) {
            return;
        }

        if (subscribedConversationId) {
            echo.leave(`conversation.${subscribedConversationId}`);
        }

        subscribedConversationId = conversationId || null;

        if (!conversationId) {
            return;
        }

        echo.private(`conversation.${conversationId}`)
            .listen('.message.sent', (event) => {
                dispatch('echo:message.sent', { message: event.message ?? event });
            })
            .listen('.messages.read', (event) => {
                dispatch('echo:messages.read', event);
            })
            .listen('.user.typing', (event) => {
                dispatch('echo:user.typing', event);
            });
    };

    window.messagingSubscribeConversation(config.activeConversationId);
}

document.addEventListener('DOMContentLoaded', initMessagingEcho);

export {};

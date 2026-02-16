import { onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

export function useTabSync(keysToReload = [], enableAutoReload = true) {
    let channel = null;
    const page = usePage();

    const refreshData = () => {
        // Defensive check: ensure router is initialized and we have a page
        if (!router || !page.props) return;

        console.log('Syncing tab data...');
        try {
            const options = {
                preserveScroll: true,
                preserveState: true,
            };

            // Only add 'only' if we actually want partial reloads
            if (Array.isArray(keysToReload) && keysToReload.length > 0) {
                options.only = [...keysToReload, 'flash', 'errors'];
            }

            router.reload(options);
        } catch (error) {
            console.error('Tab sync failed:', error);
        }
    };

    const handleVisibilityChange = () => {
        if (document.visibilityState === 'visible' && enableAutoReload) {
            refreshData();
        }
    };

    const handleTabSyncMessage = (event) => {
        if (event.data === 'data-updated') {
            console.log('Received update signal from another tab, refreshing...');
            if (document.visibilityState === 'visible' && enableAutoReload) {
                refreshData();
            }
        }
    };

    const broadcastUpdate = () => {
        if (channel) {
            channel.postMessage('data-updated');
        }
    };

    onMounted(() => {
        document.addEventListener('visibilitychange', handleVisibilityChange);

        if ('BroadcastChannel' in window) {
            channel = new BroadcastChannel('journal_sync');
            channel.onmessage = handleTabSyncMessage;
        }
    });

    onUnmounted(() => {
        document.removeEventListener('visibilitychange', handleVisibilityChange);
        if (channel) {
            channel.close();
        }
    });

    return {
        broadcastUpdate
    };
}

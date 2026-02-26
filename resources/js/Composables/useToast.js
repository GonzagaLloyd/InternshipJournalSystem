import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const toasts = ref([]);
const seenFlashes = new Set(); // Track processed flash messages to prevent duplicates

// Clear the 'seen' registry whenever a new request begins.
// This ensures that if the server sends the same message twice (e.g., two deletions),
// the second one is treated as fresh and shown to the user.
router.on('start', () => {
    seenFlashes.clear();
});

export function useToast() {
    const addToast = (message, type = 'success', duration = 5000) => {
        // Create a unique fingerprint for this message
        const fingerprint = `${type}:${message}:${Date.now()}`;

        const id = Date.now();
        toasts.value.push({
            id,
            message,
            type,
            duration
        });

        if (duration > 0) {
            setTimeout(() => {
                removeToast(id);
            }, duration);
        }
    };

    const removeToast = (id) => {
        const index = toasts.value.findIndex(t => t.id === id);
        if (index !== -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (message, duration) => addToast(message, 'success', duration);
    const error = (message, duration) => addToast(message, 'error', duration);

    /**
     * Specifically for Inertia flash messages.
     * Prevents showing the same flash multiple times during page transitions.
     */
    const processInertiaFlash = (flash) => {
        if (!flash || (Object.keys(flash).length === 0)) return;

        // Use a stringified version as the key to track this specific flash state
        const flashKey = JSON.stringify(flash);
        if (seenFlashes.has(flashKey)) return;
        seenFlashes.add(flashKey);

        // Deduplicate: check if we already have this exact message in the active toasts
        // This handles cases where multiple components might call this simultaneously
        if (flash.success && !toasts.value.some(t => t.message === flash.success)) {
            addToast(flash.success, 'success');
        }
        if (flash.error && !toasts.value.some(t => t.message === flash.error)) {
            addToast(flash.error, 'error');
        }
    };

    return {
        toasts,
        addToast,
        removeToast,
        success,
        error,
        processInertiaFlash
    };
}


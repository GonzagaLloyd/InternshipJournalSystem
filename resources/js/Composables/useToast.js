import { ref } from 'vue';

const toasts = ref([]);

export function useToast() {
    const addToast = (message, type = 'success', duration = 5000) => {
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

    return {
        toasts,
        addToast,
        removeToast,
        success,
        error
    };
}

import { reactive } from 'vue';

export const toasts = reactive([]);

export function toast(message, type = 'success') {
    const id = Date.now() + Math.random();
    toasts.push({ id, message, type });
    setTimeout(() => {
        const index = toasts.findIndex((t) => t.id === id);
        if (index !== -1) toasts.splice(index, 1);
    }, 3500);
}

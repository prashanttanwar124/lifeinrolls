import { router } from '@inertiajs/vue3';
import ToastEventBus from 'primevue/toasteventbus';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
    router.on('flash', (event) => {
        const flash = (event as CustomEvent).detail?.flash;
        const data = flash?.toast as FlashToast | undefined;

        if (!data) {
            return;
        }

        const severityMap: Record<string, 'success' | 'info' | 'warn' | 'error'> = {
            success: 'success',
            error: 'error',
            info: 'info',
            warning: 'warn',
        };

        ToastEventBus.emit('add', {
            severity: severityMap[data.type] || 'info',
            summary: data.type.charAt(0).toUpperCase() + data.type.slice(1),
            detail: data.message,
            life: 4000,
        });
    });
}

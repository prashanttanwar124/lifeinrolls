<script setup lang="ts">
import { usePasskeyRegister } from '@laravel/passkeys/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

const emit = defineEmits<{
    success: [];
}>();

const getDefaultPasskeyName = () => {
    const ua = navigator.userAgent;

    const browser = [
        { pattern: /Edg|Edge/, name: 'Edge' },
        { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
        { pattern: /Firefox|FxiOS/, name: 'Firefox' },
        { pattern: /Chrome|CriOS/, name: 'Chrome' },
        { pattern: /Safari/, name: 'Safari' },
    ].find(({ pattern }) => pattern.test(ua))?.name;

    const os = [
        { pattern: /iPhone/, name: 'iPhone' },
        { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
        { pattern: /Android/, name: 'Android' },
        { pattern: /Mac/, name: 'Mac' },
        { pattern: /Windows/, name: 'Windows' },
    ].find(({ pattern }) => pattern.test(ua))?.name;

    return [browser, os].filter(Boolean).join(' on ') || '';
};

const name = ref(getDefaultPasskeyName());
const showForm = ref(false);

const { register, isLoading, error, isSupported } = usePasskeyRegister({
    onSuccess: () => {
        name.value = '';
        showForm.value = false;
        emit('success');
    },
});

const handleSubmit = async (event: Event) => {
    event.preventDefault();

    if (!name.value.trim()) {
        return;
    }

    await register(name.value);
};

const handleCancel = () => {
    showForm.value = false;
    name.value = '';
};
</script>

<template>
    <div v-if="!isSupported" class="text-sm text-slate-500">
        Passkeys are not supported in this browser.
    </div>

    <Button v-else-if="!showForm" label="Add passkey" icon="pi pi-key" severity="secondary" outlined @click="showForm = true" />

    <form
        v-else
        @submit="handleSubmit"
        class="space-y-4 rounded-lg border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900/50 p-4"
    >
        <div class="grid gap-2">
            <label for="passkey-name" class="font-medium text-sm">Passkey name</label>
            <InputText
                id="passkey-name"
                type="text"
                v-model="name"
                placeholder="e.g., MacBook Pro, iPhone"
                class="mt-1 block w-full"
                autofocus
            />
            <p class="text-xs text-slate-500">
                A name helps you identify this passkey later.
            </p>
        </div>

        <InputError v-if="error" :message="error" />

        <div class="flex gap-2">
            <Button
                type="submit"
                :label="isLoading ? 'Registering...' : 'Register passkey'"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-plus'"
                :disabled="isLoading || !name.trim()"
            />
            <Button type="button" label="Cancel" severity="secondary" text @click="handleCancel" />
        </div>
    </form>
</template>

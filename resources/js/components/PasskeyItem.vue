<script setup lang="ts">
import { ref } from 'vue';
import type { Passkey } from '@/types/auth';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';

const props = defineProps<{
    passkey: Passkey;
}>();

const emit = defineEmits<{
    remove: [id: number, onError: () => void];
}>();

const isDeleting = ref(false);
const showDialog = ref(false);

const handleDelete = () => {
    isDeleting.value = true;
    emit('remove', props.passkey.id, () => {
        isDeleting.value = false;
    });
};
</script>

<template>
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-zinc-800 p-4 last:border-b-0">
        <div class="flex items-center gap-4">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400"
            >
                <i class="pi pi-key text-lg"></i>
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <p class="font-medium tracking-tight">{{ passkey.name }}</p>
                    <Tag
                        v-if="passkey.authenticator"
                        :value="passkey.authenticator"
                        severity="secondary"
                        size="small"
                    />
                </div>
                <p class="text-sm text-slate-500">
                    Added {{ passkey.created_at_diff }}
                    <template v-if="passkey.last_used_at_diff">
                        <span class="mx-1">/</span>
                        Last used {{ passkey.last_used_at_diff }}
                    </template>
                </p>
            </div>
        </div>

        <Button
            icon="pi pi-trash"
            severity="danger"
            text
            rounded
            aria-label="Remove passkey"
            @click="showDialog = true"
        />

        <Dialog
            v-model:visible="showDialog"
            modal
            header="Remove passkey"
            :style="{ width: '28rem' }"
        >
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Are you sure you want to remove the "{{ passkey.name }}" passkey? You will no longer be able to use it to sign in.
            </p>
            <div class="flex justify-end gap-2">
                <Button label="Cancel" severity="secondary" text @click="showDialog = false" />
                <Button
                    label="Remove passkey"
                    severity="danger"
                    :icon="isDeleting ? 'pi pi-spin pi-spinner' : 'pi pi-trash'"
                    :loading="isDeleting"
                    @click="handleDelete"
                />
            </div>
        </Dialog>
    </div>
</template>

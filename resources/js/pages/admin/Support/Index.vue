<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';

defineProps<{
    tickets: {
        data: any[];
    };
}>();

const selectedTicket = ref<any>(null);
const showDialog = ref(false);

const form = useForm({
    admin_response: '',
});

const openReplyModal = (ticket: any) => {
    selectedTicket.value = ticket;
    form.admin_response = ticket.admin_response || '';
    showDialog.value = true;
};

const submitReply = () => {
    if (!selectedTicket.value) return;

    form.post(`/admin/support/${selectedTicket.value.id}/reply`, {
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Support Tickets" />

    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Support Requests & Assistance</h1>
            <p class="text-sm text-slate-500">Handle customer support tickets submitted from the mobile app.</p>
        </div>

        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #content>
                <DataTable :value="tickets.data" responsiveLayout="scroll">
                    <Column field="id" header="ID"></Column>
                    <Column field="user.name" header="User"></Column>
                    <Column field="subject" header="Subject"></Column>
                    <Column field="status" header="Status">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="slotProps.data.status === 'open' ? 'warn' : 'success'"
                                class="capitalize"
                            />
                        </template>
                    </Column>
                    <Column field="created_at" header="Submitted">
                        <template #body="slotProps">
                            <span class="text-xs text-slate-500">{{ new Date(slotProps.data.created_at).toLocaleDateString() }}</span>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <Button
                                label="Reply"
                                icon="pi pi-reply"
                                severity="emerald"
                                text
                                size="small"
                                @click="openReplyModal(slotProps.data)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- Reply Ticket Dialog -->
        <Dialog v-model:visible="showDialog" header="Support Request Reply" modal :style="{ width: '32rem' }">
            <div v-if="selectedTicket" class="space-y-4">
                <div class="p-3 bg-slate-100 dark:bg-zinc-800 rounded-lg text-sm">
                    <div class="font-bold mb-1">{{ selectedTicket.subject }}</div>
                    <p class="text-slate-600 dark:text-slate-300">{{ selectedTicket.message }}</p>
                </div>

                <form @submit.prevent="submitReply" class="space-y-3">
                    <div class="grid gap-2">
                        <label for="response" class="text-sm font-medium">Your Response</label>
                        <Textarea id="response" v-model="form.admin_response" rows="4" required class="w-full" placeholder="Type message to send to user..." />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <Button label="Cancel" severity="secondary" text @click="showDialog = false" />
                        <Button type="submit" label="Send Reply & Close Ticket" severity="emerald" :loading="form.processing" />
                    </div>
                </form>
            </div>
        </Dialog>
    </div>
</template>

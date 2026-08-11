<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Tag from 'primevue/tag';

defineProps<{
    reports: {
        data: any[];
    };
}>();

const dismissReport = (id: number) => {
    router.post(`/admin/reports/${id}/dismiss`, {}, { preserveScroll: true });
};

const deletePhoto = (id: number) => {
    if (confirm('Delete reported photo permanently?')) {
        router.delete(`/admin/reports/${id}/photo`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Reported Photos Review" />

    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Reported Content Moderation</h1>
            <p class="text-sm text-slate-500">Review photos reported by app users for inappropriate content.</p>
        </div>

        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #content>
                <DataTable :value="reports.data" responsiveLayout="scroll">
                    <Column field="id" header="ID"></Column>
                    <Column field="photo.photo_url" header="Photo Preview">
                        <template #body="slotProps">
                            <img
                                v-if="slotProps.data.photo?.photo_url"
                                :src="slotProps.data.photo.photo_url"
                                alt="Reported Photo"
                                class="w-16 h-16 object-cover rounded-lg border border-slate-200 dark:border-zinc-800"
                            />
                            <span v-else class="text-xs text-slate-400">Photo deleted</span>
                        </template>
                    </Column>
                    <Column field="photo.film_roll.title" header="Film Roll"></Column>
                    <Column field="user.name" header="Reported By"></Column>
                    <Column field="reason" header="Reason"></Column>
                    <Column field="status" header="Status">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.status"
                                :severity="slotProps.data.status === 'pending' ? 'danger' : 'secondary'"
                                class="capitalize"
                            />
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <Button
                                    label="Dismiss"
                                    severity="secondary"
                                    text
                                    size="small"
                                    @click="dismissReport(slotProps.data.id)"
                                />
                                <Button
                                    label="Delete Photo"
                                    severity="danger"
                                    size="small"
                                    icon="pi pi-trash"
                                    @click="deletePhoto(slotProps.data.id)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </div>
</template>

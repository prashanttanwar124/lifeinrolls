<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';

defineProps<{
    banners: any[];
}>();

const showDialog = ref(false);

const form = useForm({
    title: '',
    message: '',
    target_role: 'all',
});

const submit = () => {
    form.post('/admin/banners', {
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Banners & Push Notifications" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Banners & Announcements</h1>
                <p class="text-sm text-slate-500">Publish in-app banners, offers, and announcements to mobile users.</p>
            </div>
            <Button label="New Announcement" icon="pi pi-megaphone" severity="emerald" @click="showDialog = true" />
        </div>

        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #content>
                <DataTable :value="banners" responsiveLayout="scroll">
                    <Column field="id" header="ID"></Column>
                    <Column field="title" header="Title"></Column>
                    <Column field="message" header="Message"></Column>
                    <Column field="target_role" header="Target">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.target_role" severity="secondary" class="uppercase text-xs" />
                        </template>
                    </Column>
                    <Column field="published_at" header="Published">
                        <template #body="slotProps">
                            <span class="text-xs text-slate-500">{{ new Date(slotProps.data.published_at).toLocaleDateString() }}</span>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- New Banner Dialog -->
        <Dialog v-model:visible="showDialog" header="Publish Announcement Banner" modal :style="{ width: '30rem' }">
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <label for="title" class="text-sm font-medium">Banner Title</label>
                    <InputText id="title" v-model="form.title" required placeholder="e.g. Summer Photo Contest!" class="w-full" />
                </div>

                <div class="grid gap-2">
                    <label for="message" class="text-sm font-medium">Message Body</label>
                    <Textarea id="message" v-model="form.message" rows="3" required class="w-full" placeholder="Enter details..." />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button label="Cancel" severity="secondary" text @click="showDialog = false" />
                    <Button type="submit" label="Publish Banner" severity="emerald" :loading="form.processing" />
                </div>
            </form>
        </Dialog>
    </div>
</template>

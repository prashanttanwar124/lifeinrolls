<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import { ref } from 'vue';

const props = defineProps<{
    rolls: {
        data: any[];
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

const handleSearch = () => {
    router.get('/admin/rolls', { search: search.value }, { preserveState: true });
};

const deleteRoll = (rollId: number) => {
    if (confirm('Are you sure you want to delete this film roll? All uploaded photos will be deleted.')) {
        router.delete(`/admin/rolls/${rollId}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Film Rolls Management" />

    <div class="flex flex-col gap-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Film Rolls Management</h1>
                <p class="text-sm text-slate-500">Monitor active, completed, and shared customer film rolls.</p>
            </div>
            <div class="flex items-center gap-2">
                <InputText
                    v-model="search"
                    placeholder="Search by title or invite code..."
                    class="w-64"
                    @keyup.enter="handleSearch"
                />
                <Button icon="pi pi-search" @click="handleSearch" />
            </div>
        </div>

        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #content>
                <DataTable :value="rolls.data" responsiveLayout="scroll">
                    <Column field="id" header="ID"></Column>
                    <Column field="title" header="Title"></Column>
                    <Column field="invite_code" header="Invite Code">
                        <template #body="slotProps">
                            <code class="px-2 py-1 bg-slate-100 dark:bg-zinc-800 rounded font-mono text-xs">{{ slotProps.data.invite_code }}</code>
                        </template>
                    </Column>
                    <Column field="creator.name" header="Creator"></Column>
                    <Column field="current_photos" header="Exposures">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold">{{ slotProps.data.current_photos }} / {{ slotProps.data.max_photos }}</span>
                        </template>
                    </Column>
                    <Column field="roll_type" header="Type">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.roll_type" severity="secondary" class="capitalize" />
                        </template>
                    </Column>
                    <Column field="status" header="Status">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="slotProps.data.status === 'active' ? 'success' : 'warn'" class="capitalize" />
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                text
                                rounded
                                size="small"
                                @click="deleteRoll(slotProps.data.id)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </div>
</template>

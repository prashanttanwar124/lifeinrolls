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
    users: {
        data: any[];
        total: number;
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

const handleSearch = () => {
    router.get('/admin/users', { search: search.value }, { preserveState: true });
};

const toggleRole = (userId: number) => {
    router.post(`/admin/users/${userId}/toggle-role`, {}, { preserveScroll: true });
};

const deleteUser = (userId: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(`/admin/users/${userId}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="User Management" />

    <div class="flex flex-col gap-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">User Management</h1>
                <p class="text-sm text-slate-500">View and manage registered mobile & web app users.</p>
            </div>
            <div class="flex items-center gap-2">
                <InputText
                    v-model="search"
                    placeholder="Search by name or email..."
                    class="w-64"
                    @keyup.enter="handleSearch"
                />
                <Button icon="pi pi-search" @click="handleSearch" />
            </div>
        </div>

        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #content>
                <DataTable :value="users.data" responsiveLayout="scroll">
                    <Column field="id" header="ID"></Column>
                    <Column field="name" header="Name"></Column>
                    <Column field="email" header="Email"></Column>
                    <Column field="role" header="Role">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.role"
                                :severity="slotProps.data.role === 'admin' ? 'info' : 'secondary'"
                                class="capitalize"
                            />
                        </template>
                    </Column>
                    <Column field="created_at" header="Registered">
                        <template #body="slotProps">
                            <span class="text-xs text-slate-500">{{ new Date(slotProps.data.created_at).toLocaleDateString() }}</span>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <Button
                                    :label="slotProps.data.role === 'admin' ? 'Demote' : 'Make Admin'"
                                    severity="secondary"
                                    text
                                    size="small"
                                    @click="toggleRole(slotProps.data.id)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    severity="danger"
                                    text
                                    rounded
                                    size="small"
                                    @click="deleteUser(slotProps.data.id)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </div>
</template>

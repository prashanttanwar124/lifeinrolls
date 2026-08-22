<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import { ref } from 'vue';

const props = defineProps<{
    stats: {
        total_users: number;
        total_rolls: number;
        active_rolls: number;
        total_photos: number;
        pending_reports: number;
        open_support: number;
        total_presets: number;
    };
    recentRolls: any[];
    recentUsers: any[];
    storageInfo?: {
        disk: string;
        bucket?: string;
        endpoint?: string;
        url?: string;
        has_key: boolean;
        has_secret: boolean;
    };
}>();

const testingStorage = ref(false);

const runStorageTest = () => {
    testingStorage.value = true;
    router.post('/admin/storage-test', {}, {
        preserveScroll: true,
        onFinish: () => {
            testingStorage.value = false;
        },
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="flex flex-col gap-6">
        <!-- Error Banner if Storage test failed -->
        <div v-if="$page.props.errors?.storage_error" class="p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300">
            <div class="flex items-start gap-3">
                <i class="pi pi-times-circle text-xl mt-0.5 text-red-500"></i>
                <div class="space-y-1 text-sm font-medium">
                    <p class="font-bold text-red-800 dark:text-red-200">Cloudflare R2 / Storage Test Failed:</p>
                    <p class="font-mono text-xs break-all">{{ $page.props.errors.storage_error }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Admin Overview</h1>
                <p class="text-sm text-slate-500">Manage mobile app users, rolls, presets, and storage.</p>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    label="Test R2 Storage Connection"
                    icon="pi pi-cloud"
                    severity="secondary"
                    outlined
                    size="small"
                    :loading="testingStorage"
                    @click="runStorageTest"
                />
                <Tag value="System Admin" severity="info" icon="pi pi-shield" />
            </div>
        </div>

        <!-- Storage Status Banner -->
        <div v-if="storageInfo" class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-orange-100 dark:bg-orange-950 flex items-center justify-center text-orange-600">
                    <i class="pi pi-cloud-upload text-base"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold">Storage Driver:</span>
                        <Tag :value="storageInfo.disk" :severity="storageInfo.disk === 's3' ? 'success' : 'warn'" class="uppercase text-xs" />
                        <span v-if="storageInfo.bucket" class="text-xs text-slate-500 font-mono">Bucket: {{ storageInfo.bucket }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Key: {{ storageInfo.has_key ? '✓ Configured' : '✗ Missing' }} ·
                        Secret: {{ storageInfo.has_secret ? '✓ Configured' : '✗ Missing' }} ·
                        Public URL: {{ storageInfo.url || 'Not set' }}
                    </p>
                </div>
            </div>

            <Button
                label="Run Live Upload & Read Test"
                icon="pi pi-play"
                size="small"
                severity="primary"
                :loading="testingStorage"
                @click="runStorageTest"
            />
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
                <template #content>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-slate-500 font-medium">Total Users</span>
                            <div class="text-2xl font-bold mt-1">{{ stats.total_users }}</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center text-emerald-600">
                            <i class="pi pi-users text-lg"></i>
                        </div>
                    </div>
                </template>
            </Card>

            <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
                <template #content>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-slate-500 font-medium">Active Rolls</span>
                            <div class="text-2xl font-bold mt-1">{{ stats.active_rolls }} / {{ stats.total_rolls }}</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-950 flex items-center justify-center text-blue-600">
                            <i class="pi pi-camera text-lg"></i>
                        </div>
                    </div>
                </template>
            </Card>

            <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
                <template #content>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-slate-500 font-medium">Reported Content</span>
                            <div class="text-2xl font-bold mt-1 text-amber-600">{{ stats.pending_reports }}</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-950 flex items-center justify-center text-amber-600">
                            <i class="pi pi-flag text-lg"></i>
                        </div>
                    </div>
                </template>
            </Card>

            <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
                <template #content>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-slate-500 font-medium">Open Support</span>
                            <div class="text-2xl font-bold mt-1 text-purple-600">{{ stats.open_support }}</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-950 flex items-center justify-center text-purple-600">
                            <i class="pi pi-comments text-lg"></i>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Recent Film Rolls Table -->
        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #title>
                <div class="flex items-center justify-between">
                    <span class="text-base font-bold">Recent Film Rolls</span>
                    <Link href="/admin/rolls">
                        <Button label="View All Rolls" icon="pi pi-arrow-right" iconPos="right" text size="small" />
                    </Link>
                </div>
            </template>
            <template #content>
                <DataTable :value="recentRolls" responsiveLayout="scroll">
                    <Column field="title" header="Title"></Column>
                    <Column field="invite_code" header="Invite Code">
                        <template #body="slotProps">
                            <code class="px-2 py-1 bg-slate-100 dark:bg-zinc-800 rounded font-mono text-xs">{{ slotProps.data.invite_code }}</code>
                        </template>
                    </Column>
                    <Column field="creator.name" header="Creator"></Column>
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
                </DataTable>
            </template>
        </Card>
    </div>
</template>

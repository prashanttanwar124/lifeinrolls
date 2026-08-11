<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Tag from 'primevue/tag';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const page = usePage();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6">
        <!-- Welcome Hero Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 p-6 text-white shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Welcome back, {{ page.props.auth?.user?.name || 'User' }}!</h1>
                    <p class="text-emerald-100 text-sm mt-1">Here is your LifeInRolls film camera application dashboard.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Tag value="Active Roll" severity="warn" icon="pi pi-camera" />
                    <Button label="New Film Roll" icon="pi pi-plus" severity="secondary" rounded />
                </div>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
                <template #title>
                    <div class="flex items-center justify-between text-sm text-slate-500 font-medium">
                        <span>Total Film Rolls</span>
                        <i class="pi pi-camera text-emerald-500 text-lg"></i>
                    </div>
                </template>
                <template #content>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white mt-1">12</div>
                    <div class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1">
                        <i class="pi pi-arrow-up text-xs"></i>
                        <span>+2 rolls this month</span>
                    </div>
                </template>
            </Card>

            <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
                <template #title>
                    <div class="flex items-center justify-between text-sm text-slate-500 font-medium">
                        <span>Photos Captured</span>
                        <i class="pi pi-images text-teal-500 text-lg"></i>
                    </div>
                </template>
                <template #content>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white mt-1">432</div>
                    <div class="text-xs text-teal-600 font-semibold mt-2 flex items-center gap-1">
                        <i class="pi pi-arrow-up text-xs"></i>
                        <span>36 frames per roll</span>
                    </div>
                </template>
            </Card>

            <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
                <template #title>
                    <div class="flex items-center justify-between text-sm text-slate-500 font-medium">
                        <span>Storage Used</span>
                        <i class="pi pi-database text-blue-500 text-lg"></i>
                    </div>
                </template>
                <template #content>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white mt-1">2.4 GB</div>
                    <div class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <span>RAW & JPEG Exports</span>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Recent Activity Section -->
        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #title>
                <div class="flex items-center justify-between">
                    <span class="text-lg font-bold">Recent Film Activity</span>
                    <Button label="View All" icon="pi pi-arrow-right" iconPos="right" text severity="emerald" size="small" />
                </div>
            </template>
            <template #content>
                <div class="divide-y divide-slate-100 dark:divide-zinc-800">
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center text-emerald-600">
                                <i class="pi pi-image text-lg"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Kodak Portra 400 - Tokyo Trip</div>
                                <div class="text-xs text-slate-500">36 / 36 frames exposed</div>
                            </div>
                        </div>
                        <Tag value="Completed" severity="success" />
                    </div>

                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-950 flex items-center justify-center text-amber-600">
                                <i class="pi pi-camera text-lg"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Fuji Superia 400 - Summer Walk</div>
                                <div class="text-xs text-slate-500">18 / 36 frames exposed</div>
                            </div>
                        </div>
                        <Tag value="In Progress" severity="warn" />
                    </div>

                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-950 flex items-center justify-center text-purple-600">
                                <i class="pi pi-sparkles text-lg"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Cinestill 800T - Night Street</div>
                                <div class="text-xs text-slate-500">Developing in progress</div>
                            </div>
                        </div>
                        <Tag value="Processing" severity="info" />
                    </div>
                </div>
            </template>
        </Card>
    </div>
</template>

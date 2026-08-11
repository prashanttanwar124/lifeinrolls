<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Card from 'primevue/card';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-slate-50 dark:bg-zinc-950 p-6 md:p-10"
    >
        <div class="w-full max-w-md">
            <Card class="border border-slate-200 dark:border-zinc-800 shadow-lg">
                <template #header>
                    <div class="pt-6 px-6 text-center">
                        <Link href="/dashboard" class="inline-flex items-center gap-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            <i class="pi pi-camera text-3xl text-emerald-500"></i>
                            <span>LifeInRolls</span>
                        </Link>
                        <h1 v-if="title" class="mt-4 text-xl font-bold text-slate-900 dark:text-white">
                            {{ title }}
                        </h1>
                        <p v-if="description" class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ description }}
                        </p>
                    </div>
                </template>
                <template #content>
                    <slot />
                </template>
            </Card>
        </div>
    </div>
</template>

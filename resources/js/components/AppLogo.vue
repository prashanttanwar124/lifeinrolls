<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const name = usePage().props.name || 'Life in Rolls';
</script>

<template>
    <div class="flex items-center gap-2.5">
        <AppLogoIcon class="size-8 rounded-lg shadow-md" />
        <div class="grid flex-1 text-left text-sm">
            <span class="truncate leading-tight font-bold bg-gradient-to-r from-[#FFB649] via-[#FF7253] to-[#EB2A74] bg-clip-text text-transparent">
                {{ name }}
            </span>
            <span class="text-[10px] tracking-wider font-semibold text-neutral-400 dark:text-neutral-500 uppercase">
                Analog Studio
            </span>
        </div>
    </div>
</template>

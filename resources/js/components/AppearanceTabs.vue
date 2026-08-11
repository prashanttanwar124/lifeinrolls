<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { useLayout } from '@/composables/useLayout';

const { appearance, updateAppearance } = useAppearance();
const { layoutConfig } = useLayout();

const tabs = [
    { value: 'light', icon: 'pi pi-sun', label: 'Light' },
    { value: 'dark', icon: 'pi pi-moon', label: 'Dark' },
    { value: 'system', icon: 'pi pi-desktop', label: 'System' },
] as const;

const handleSelect = (value: 'light' | 'dark' | 'system') => {
    updateAppearance(value);
    if (value === 'dark') {
        layoutConfig.darkTheme = true;
    } else if (value === 'light') {
        layoutConfig.darkTheme = false;
    }
};
</script>

<template>
    <div
        class="inline-flex gap-1 rounded-lg bg-slate-100 p-1 dark:bg-zinc-800"
    >
        <button
            v-for="{ value, icon, label } in tabs"
            :key="value"
            @click="handleSelect(value)"
            :class="[
                'flex items-center rounded-md px-3.5 py-1.5 transition-colors cursor-pointer',
                appearance === value
                    ? 'bg-white shadow-xs dark:bg-zinc-700 dark:text-white font-medium'
                    : 'text-slate-500 hover:bg-slate-200/60 hover:text-black dark:text-slate-400 dark:hover:bg-zinc-700/60',
            ]"
        >
            <i :class="[icon, 'text-sm']"></i>
            <span class="ml-2 text-sm">{{ label }}</span>
        </button>
    </div>
</template>

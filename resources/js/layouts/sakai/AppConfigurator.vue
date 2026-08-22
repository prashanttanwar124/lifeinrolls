<script setup lang="ts">
import { useLayout } from '@/composables/useLayout';
import { updatePreset, updateSurfacePalette } from '@primevue/themes';
import Aura from '@primevue/themes/aura';
import Lara from '@primevue/themes/lara';
import Nora from '@primevue/themes/nora';
import Button from 'primevue/button';
import Drawer from 'primevue/drawer';
import RadioButton from 'primevue/radiobutton';

import { ref } from 'vue';

const { layoutConfig, isDarkTheme, toggleDarkMode } = useLayout();
const visible = ref(false);

const presets: Record<string, any> = {
    Aura,
    Lara,
    Nora,
};

const preset = ref(layoutConfig.preset);
const presetOptions = ref(['Aura', 'Lara', 'Nora']);

const primaryColors = ref([
    { name: 'coral', palette: { 50: '#fff5f2', 100: '#ffe8e2', 200: '#ffd3c7', 300: '#ffb3a1', 400: '#ff8a6e', 500: '#FF7253', 600: '#f05633', 700: '#ca3e1c', 800: '#a63419', 900: '#87301b', 950: '#491509' } },
    { name: 'amber', palette: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f', 950: '#451a03' } },
    { name: 'emerald', palette: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b', 950: '#022c22' } },
    { name: 'teal', palette: { 50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e', 800: '#115e59', 900: '#134e4a', 950: '#042f2e' } },
    { name: 'blue', palette: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#172554' } },
    { name: 'indigo', palette: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81', 950: '#1e1b4b' } },
    { name: 'violet', palette: { 50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd', 400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9', 800: '#5b21b6', 900: '#4c1d95', 950: '#2e1065' } },
]);

const selectedPrimary = ref(layoutConfig.primary);

const updatePrimary = (color: any) => {
    selectedPrimary.value = color.name;
    layoutConfig.primary = color.name;

    updatePreset({
        semantic: {
            primary: color.palette,
        },
    });
};

const updatePresetSelection = (val: string) => {
    preset.value = val;
    layoutConfig.preset = val;
    const selected = presets[val];
    if (selected) {
        updatePreset(selected);
    }
};
</script>

<template>
    <Button
        icon="pi pi-cog"
        text
        rounded
        severity="secondary"
        aria-label="Configurator"
        @click="visible = true"
    />

    <Drawer v-model:visible="visible" header="Theme Configurator" position="right" class="w-80">
        <div class="flex flex-col gap-6">
            <!-- Primary Color Selection -->
            <div>
                <span class="text-sm font-semibold block mb-3 text-slate-700 dark:text-slate-300">Primary Color</span>
                <div class="flex flex-wrap gap-3">
                    <button
                        v-for="color in primaryColors"
                        :key="color.name"
                        type="button"
                        class="w-6 h-6 rounded-full cursor-pointer transition-transform hover:scale-110 flex items-center justify-center"
                        :style="{ backgroundColor: color.palette[500] }"
                        @click="updatePrimary(color)"
                    >
                        <i v-if="selectedPrimary === color.name" class="pi pi-check text-xs text-white"></i>
                    </button>
                </div>
            </div>

            <!-- Preset Selection -->
            <div>
                <span class="text-sm font-semibold block mb-3 text-slate-700 dark:text-slate-300">Theme Preset</span>
                <div class="flex flex-col gap-2">
                    <div v-for="p in presetOptions" :key="p" class="flex items-center gap-2">
                        <RadioButton
                            :inputId="p"
                            name="preset"
                            :value="p"
                            :modelValue="preset"
                            @update:modelValue="updatePresetSelection"
                        />
                        <label :for="p" class="text-sm cursor-pointer capitalize font-medium">{{ p }}</label>
                    </div>
                </div>
            </div>

            <!-- Menu Mode Selection -->
            <div>
                <span class="text-sm font-semibold block mb-3 text-slate-700 dark:text-slate-300">Menu Mode</span>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <RadioButton
                            inputId="mode-static"
                            name="menuMode"
                            value="static"
                            v-model="layoutConfig.menuMode"
                        />
                        <label for="mode-static" class="text-sm cursor-pointer font-medium">Static</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <RadioButton
                            inputId="mode-overlay"
                            name="menuMode"
                            value="overlay"
                            v-model="layoutConfig.menuMode"
                        />
                        <label for="mode-overlay" class="text-sm cursor-pointer font-medium">Overlay</label>
                    </div>
                </div>
            </div>

            <!-- Dark Theme Toggle -->
            <div>
                <span class="text-sm font-semibold block mb-3 text-slate-700 dark:text-slate-300">Appearance</span>
                <Button
                    :label="isDarkTheme ? 'Switch to Light' : 'Switch to Dark'"
                    :icon="isDarkTheme ? 'pi pi-sun' : 'pi pi-moon'"
                    severity="secondary"
                    outlined
                    class="w-full"
                    @click="toggleDarkMode"
                />
            </div>
        </div>
    </Drawer>
</template>

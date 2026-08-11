<script setup lang="ts">
import { useLayout } from '@/composables/useLayout';
import Toast from 'primevue/toast';
import { computed } from 'vue';
import AppFooter from './sakai/AppFooter.vue';
import AppSidebar from './sakai/AppSidebar.vue';
import AppTopbar from './sakai/AppTopbar.vue';

const { layoutConfig, layoutState } = useLayout();

const containerClass = computed(() => {
    return {
        'layout-overlay': layoutConfig.menuMode === 'overlay',
        'layout-static': layoutConfig.menuMode === 'static',
        'layout-static-inactive':
            layoutState.staticMenuInactive && layoutConfig.menuMode === 'static',
        'layout-overlay-active': layoutState.overlayMenuActive,
        'layout-mobile-active': layoutState.overlayMenuActive,
    };
});
</script>

<template>
    <div class="layout-wrapper" :class="containerClass">
        <AppTopbar />
        <AppSidebar />

        <div class="layout-main-container">
            <main class="layout-main">
                <slot />
            </main>
            <AppFooter />
        </div>

        <Toast />
    </div>
</template>

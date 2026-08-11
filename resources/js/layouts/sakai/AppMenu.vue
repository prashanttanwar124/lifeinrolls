<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppMenuItem from './AppMenuItem.vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

const model = computed(() => [
    {
        label: 'Home',
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' },
        ],
    },
    ...(isAdmin.value
        ? [
              {
                  label: 'Admin Management',
                  items: [
                      { label: 'Admin Overview', icon: 'pi pi-fw pi-chart-bar', to: '/admin/dashboard' },
                      { label: 'Users', icon: 'pi pi-fw pi-users', to: '/admin/users' },
                      { label: 'Film Rolls', icon: 'pi pi-fw pi-camera', to: '/admin/rolls' },
                      { label: 'Camera Presets', icon: 'pi pi-fw pi-sliders-h', to: '/admin/presets' },
                      { label: 'Reported Photos', icon: 'pi pi-fw pi-flag', to: '/admin/reports' },
                      { label: 'Subscription Plans', icon: 'pi pi-fw pi-credit-card', to: '/admin/subscriptions' },
                      { label: 'Support Tickets', icon: 'pi pi-fw pi-comments', to: '/admin/support' },
                      { label: 'Banners & Push', icon: 'pi pi-fw pi-megaphone', to: '/admin/banners' },
                  ],
              },
          ]
        : []),
    {
        label: 'Settings',
        items: [
            { label: 'Profile', icon: 'pi pi-fw pi-user', to: '/settings/profile' },
            { label: 'Password', icon: 'pi pi-fw pi-lock', to: '/settings/password' },
            { label: 'Appearance', icon: 'pi pi-fw pi-palette', to: '/settings/appearance' },
            { label: 'Two-Factor Auth', icon: 'pi pi-fw pi-shield', to: '/settings/two-factor' },
        ],
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item.label">
            <AppMenuItem :item="item" :index="i" :root="true" />
        </template>
    </ul>
</template>

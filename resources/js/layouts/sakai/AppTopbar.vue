<script setup lang="ts">
import { useLayout } from '@/composables/useLayout';
import { usePage, Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
import { ref } from 'vue';
import AppConfigurator from './AppConfigurator.vue';

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();
const page = usePage();
const userMenu = ref();

const menuItems = [
    {
        label: 'Settings',
        icon: 'pi pi-cog',
        command: () => router.get('/settings/profile'),
    },
    {
        separator: true,
    },
    {
        label: 'Log Out',
        icon: 'pi pi-sign-out',
        command: () => router.post('/logout'),
    },
];

const toggleUserMenu = (event: Event) => {
    userMenu.value?.toggle(event);
};
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <Button
                icon="pi pi-bars"
                text
                rounded
                severity="secondary"
                aria-label="Toggle Navigation"
                @click="toggleMenu"
            />

            <Link href="/dashboard" class="layout-topbar-logo">
                <i class="pi pi-camera text-2xl text-emerald-500"></i>
                <span class="font-bold tracking-tight">LifeInRolls</span>
            </Link>
        </div>

        <div class="layout-topbar-actions">
            <Button
                :icon="isDarkTheme ? 'pi pi-sun' : 'pi pi-moon'"
                text
                rounded
                severity="secondary"
                aria-label="Toggle Theme"
                @click="toggleDarkMode"
            />

            <AppConfigurator />

            <div v-if="page.props.auth?.user" class="relative">
                <Button
                    text
                    rounded
                    severity="secondary"
                    class="flex items-center gap-2 px-2"
                    @click="toggleUserMenu"
                >
                    <i class="pi pi-user text-lg"></i>
                    <span class="hidden md:inline font-medium text-sm">
                        {{ page.props.auth.user.name }}
                    </span>
                    <i class="pi pi-chevron-down text-xs"></i>
                </Button>
                <Menu ref="userMenu" :model="menuItems" popup />
            </div>
            <div v-else class="flex items-center gap-2">
                <Link href="/login">
                    <Button label="Log in" text severity="secondary" size="small" />
                </Link>
                <Link href="/register">
                    <Button label="Register" size="small" />
                </Link>
            </div>
        </div>
    </div>
</template>

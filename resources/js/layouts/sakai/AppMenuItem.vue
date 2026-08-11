<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    item: {
        label?: string;
        icon?: string;
        to?: string;
        items?: any[];
        separator?: boolean;
        class?: string;
    };
    index?: number;
    root?: boolean;
}>();

const page = usePage();

const isActiveRoute = computed(() => {
    if (!props.item.to) return false;
    return page.url === props.item.to || page.url.startsWith(props.item.to + '/');
});
</script>

<template>
    <li :class="[{ 'layout-root-menuitem': root }, item.class]">
        <div v-if="root && item.label" class="layout-menu-category">
            {{ item.label }}
        </div>

        <Link
            v-if="item.to"
            :href="item.to"
            :class="['layout-menuitem-link', { 'active-route': isActiveRoute }]"
        >
            <i v-if="item.icon" :class="['layout-menuitem-icon', item.icon]"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
        </Link>

        <ul v-if="item.items" class="layout-submenu">
            <AppMenuItem
                v-for="(child, i) in item.items"
                :key="child.label || i"
                :item="child"
                :index="i"
            />
        </ul>
    </li>
</template>

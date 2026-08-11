<script setup lang="ts">
import { computed } from 'vue';
import Message from 'primevue/message';

type Props = {
    errors: string[];
    title?: string;
};

const props = withDefaults(defineProps<Props>(), {
    title: 'Something went wrong.',
});

const uniqueErrors = computed(() => Array.from(new Set(props.errors)));
</script>

<template>
    <Message severity="error" icon="pi pi-exclamation-triangle" class="w-full">
        <div class="font-bold text-sm mb-1">{{ title }}</div>
        <ul class="list-inside list-disc text-sm space-y-0.5">
            <li v-for="(error, index) in uniqueErrors" :key="index">
                {{ error }}
            </li>
        </ul>
    </Message>
</template>

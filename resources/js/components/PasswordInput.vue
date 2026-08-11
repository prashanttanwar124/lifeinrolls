<script setup lang="ts">
import Password from 'primevue/password';

defineOptions({ inheritAttrs: false });

const model = defineModel<string>();
</script>

<template>
    <Password
        v-model="model"
        v-bind="$attrs"
        toggleMask
        :feedback="false"
        class="w-full"
        inputClass="w-full p-inputtext-sm"
    />
</template>

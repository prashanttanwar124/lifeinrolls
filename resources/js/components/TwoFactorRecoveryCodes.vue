<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { nextTick, onMounted, ref, useTemplateRef } from 'vue';
import AlertError from '@/components/AlertError.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { regenerateRecoveryCodes } from '@/routes/two-factor';
import Button from 'primevue/button';
import Card from 'primevue/card';

const { recoveryCodesList, fetchRecoveryCodes, errors } = useTwoFactorAuth();
const isRecoveryCodesVisible = ref<boolean>(false);
const recoveryCodeSectionRef = useTemplateRef('recoveryCodeSectionRef');

const toggleRecoveryCodesVisibility = async () => {
    if (!isRecoveryCodesVisible.value && !recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }

    isRecoveryCodesVisible.value = !isRecoveryCodesVisible.value;

    if (isRecoveryCodesVisible.value) {
        await nextTick();
        recoveryCodeSectionRef.value?.scrollIntoView({ behavior: 'smooth' });
    }
};

onMounted(async () => {
    if (!recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }
});
</script>

<template>
    <Card class="w-full border border-slate-200 dark:border-zinc-800 shadow-sm">
        <template #title>
            <div class="flex items-center gap-2 text-base font-bold">
                <i class="pi pi-shield text-emerald-500"></i>
                <span>2FA recovery codes</span>
            </div>
        </template>
        <template #subtitle>
            <span class="text-xs text-slate-500">
                Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.
            </span>
        </template>
        <template #content>
            <div
                class="flex flex-col gap-3 select-none sm:flex-row sm:items-center sm:justify-between mt-2"
            >
                <Button
                    :label="isRecoveryCodesVisible ? 'Hide recovery codes' : 'View recovery codes'"
                    :icon="isRecoveryCodesVisible ? 'pi pi-eye-slash' : 'pi pi-eye'"
                    severity="secondary"
                    @click="toggleRecoveryCodesVisibility"
                />

                <Form
                    v-if="isRecoveryCodesVisible && recoveryCodesList.length"
                    v-bind="regenerateRecoveryCodes.form()"
                    method="post"
                    :options="{ preserveScroll: true }"
                    @success="fetchRecoveryCodes"
                    #default="{ processing }"
                >
                    <Button
                        type="submit"
                        label="Regenerate codes"
                        icon="pi pi-refresh"
                        severity="secondary"
                        outlined
                        :loading="processing"
                    />
                </Form>
            </div>

            <div
                :class="[
                    'relative overflow-hidden transition-all duration-300',
                    isRecoveryCodesVisible
                        ? 'h-auto opacity-100 mt-4'
                        : 'h-0 opacity-0',
                ]"
            >
                <div v-if="errors?.length" class="mt-4">
                    <AlertError :errors="errors" />
                </div>
                <div v-else class="space-y-3">
                    <div
                        ref="recoveryCodeSectionRef"
                        class="grid gap-1 rounded-lg bg-slate-100 dark:bg-zinc-900 p-4 font-mono text-sm"
                    >
                        <div v-if="!recoveryCodesList.length" class="space-y-2">
                            <div
                                v-for="n in 8"
                                :key="n"
                                class="h-4 animate-pulse rounded bg-slate-200 dark:bg-zinc-800"
                            ></div>
                        </div>
                        <div
                            v-else
                            v-for="(code, index) in recoveryCodesList"
                            :key="index"
                        >
                            {{ code }}
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 select-none">
                        Each recovery code can be used once to access your
                        account and will be removed after use. If you need more,
                        click <span class="font-bold">Regenerate codes</span> above.
                    </p>
                </div>
            </div>
        </template>
    </Card>
</template>

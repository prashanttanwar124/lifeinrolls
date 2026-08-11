<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { computed, nextTick, ref, useTemplateRef, watch } from 'vue';
import AlertError from '@/components/AlertError.vue';
import InputError from '@/components/InputError.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { confirm } from '@/routes/two-factor';
import type { TwoFactorConfigContent } from '@/types';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Divider from 'primevue/divider';
import InputOtp from 'primevue/inputotp';
import InputText from 'primevue/inputtext';

type Props = {
    requiresConfirmation: boolean;
    twoFactorEnabled: boolean;
};

const { resolvedAppearance } = useAppearance();

const props = defineProps<Props>();
const isOpen = defineModel<boolean>('isOpen');

const { copy, copied } = useClipboard();
const { qrCodeSvg, manualSetupKey, clearSetupData, fetchSetupData, errors } =
    useTwoFactorAuth();

const showVerificationStep = ref(false);
const code = ref<string>('');

const pinInputContainerRef = useTemplateRef('pinInputContainerRef');

const modalConfig = computed<TwoFactorConfigContent>(() => {
    if (props.twoFactorEnabled) {
        return {
            title: 'Two-factor authentication enabled',
            description:
                'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
            buttonText: 'Close',
        };
    }

    if (showVerificationStep.value) {
        return {
            title: 'Verify authentication code',
            description: 'Enter the 6-digit code from your authenticator app',
            buttonText: 'Continue',
        };
    }

    return {
        title: 'Enable two-factor authentication',
        description:
            'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
        buttonText: 'Continue',
    };
});

const handleModalNextStep = () => {
    if (props.requiresConfirmation) {
        showVerificationStep.value = true;

        nextTick(() => {
            pinInputContainerRef.value?.querySelector('input')?.focus();
        });

        return;
    }

    clearSetupData();
    isOpen.value = false;
};

const resetModalState = () => {
    if (props.twoFactorEnabled) {
        clearSetupData();
    }

    showVerificationStep.value = false;
    code.value = '';
};

watch(
    () => isOpen.value,
    async (isOpen) => {
        if (!isOpen) {
            resetModalState();

            return;
        }

        if (!qrCodeSvg.value) {
            await fetchSetupData();
        }
    },
);
</script>

<template>
    <Dialog
        v-model:visible="isOpen"
        modal
        :header="modalConfig.title"
        :style="{ width: '28rem' }"
    >
        <p class="text-xs text-slate-500 text-center mb-4">
            {{ modalConfig.description }}
        </p>

        <div class="relative flex w-auto flex-col items-center justify-center space-y-4">
            <template v-if="!showVerificationStep">
                <AlertError v-if="errors?.length" :errors="errors" />
                <template v-else>
                    <div class="relative mx-auto flex max-w-md items-center overflow-hidden">
                        <div class="relative mx-auto aspect-square w-56 overflow-hidden rounded-lg border border-slate-200 dark:border-zinc-800 p-2">
                            <div
                                v-if="!qrCodeSvg"
                                class="absolute inset-0 z-10 flex aspect-square h-auto w-full animate-pulse items-center justify-center bg-slate-50 dark:bg-zinc-900"
                            >
                                <i class="pi pi-spin pi-spinner text-2xl text-emerald-500"></i>
                            </div>
                            <div
                                v-else
                                class="relative z-10 overflow-hidden p-3 flex items-center justify-center"
                            >
                                <div
                                    v-html="qrCodeSvg"
                                    class="flex aspect-square size-full items-center justify-center"
                                    :style="{
                                        filter:
                                            resolvedAppearance === 'dark'
                                                ? 'invert(1) brightness(1.5)'
                                                : undefined,
                                    }"
                                />
                            </div>
                        </div>
                    </div>

                    <Button
                        :label="modalConfig.buttonText"
                        class="w-full"
                        @click="handleModalNextStep"
                    />

                    <Divider align="center" class="w-full">
                        <span class="text-xs text-slate-500 font-medium">or, enter code manually</span>
                    </Divider>

                    <div class="flex w-full items-center justify-center gap-2">
                        <InputText
                            type="text"
                            readonly
                            :value="manualSetupKey"
                            class="w-full text-xs font-mono"
                        />
                        <Button
                            :icon="copied ? 'pi pi-check text-emerald-500' : 'pi pi-copy'"
                            severity="secondary"
                            text
                            @click="copy(manualSetupKey || '')"
                        />
                    </div>
                </template>
            </template>

            <template v-else>
                <Form
                    v-bind="confirm.form()"
                    error-bag="confirmTwoFactorAuthentication"
                    reset-on-error
                    @finish="code = ''"
                    @success="isOpen = false"
                    v-slot="{ errors, processing }"
                    class="w-full"
                >
                    <input type="hidden" name="code" :value="code" />
                    <div
                        ref="pinInputContainerRef"
                        class="relative w-full space-y-4"
                    >
                        <div class="flex flex-col items-center justify-center space-y-2 py-2">
                            <InputOtp
                                id="otp"
                                v-model="code"
                                :integerOnly="true"
                                :disabled="processing"
                                autofocus
                            />
                            <InputError :message="errors?.code" />
                        </div>

                        <div class="flex w-full items-center gap-3">
                            <Button
                                type="button"
                                label="Back"
                                severity="secondary"
                                text
                                class="flex-1"
                                @click="showVerificationStep = false"
                                :disabled="processing"
                            />
                            <Button
                                type="submit"
                                label="Confirm"
                                class="flex-1"
                                :icon="processing ? 'pi pi-spin pi-spinner' : 'pi pi-check'"
                                :disabled="processing || code.length < 6"
                                :loading="processing"
                            />
                        </div>
                    </div>
                </Form>
            </template>
        </div>
    </Dialog>
</template>

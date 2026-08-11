<script setup lang="ts">
import type { UrlMethodPair } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';
import { usePasskeyVerify } from '@laravel/passkeys/vue';
import InputError from '@/components/InputError.vue';
import Button from 'primevue/button';
import Divider from 'primevue/divider';

type Props = {
    routes?: {
        options: UrlMethodPair;
        submit: UrlMethodPair;
    };
    label?: string;
    loadingLabel?: string;
    separator?: string;
};

const props = defineProps<Props>();

const { verify, isLoading, error, isSupported } = usePasskeyVerify({
    ...(props.routes
        ? {
              routes: {
                  options: props.routes.options.url,
                  submit: props.routes.submit.url,
              },
          }
        : {}),
    onSuccess: (response) => {
        router.visit(response.redirect ?? '/dashboard');
    },
});
</script>

<template>
    <div v-if="isSupported">
        <div class="grid gap-2">
            <Button
                type="button"
                severity="secondary"
                outlined
                class="w-full"
                :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-key'"
                :label="isLoading ? (props.loadingLabel ?? 'Authenticating...') : (props.label ?? 'Sign in with a passkey')"
                :loading="isLoading"
                @click="verify"
            />

            <div v-if="error" class="text-center">
                <InputError :message="error" />
            </div>
        </div>

        <div class="relative my-4">
            <Divider align="center">
                <span class="text-xs uppercase text-slate-500 font-medium">
                    {{ props.separator ?? 'Or continue with email' }}
                </span>
            </Divider>
        </div>
    </div>
</template>

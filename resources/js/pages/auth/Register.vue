<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    <Head title="Register" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <label for="name" class="font-medium text-sm">Name</label>
                <InputText
                    id="name"
                    type="text"
                    name="name"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    placeholder="Full name"
                    class="w-full"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <label for="email" class="font-medium text-sm">Email address</label>
                <InputText
                    id="email"
                    type="email"
                    name="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="w-full"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <label for="password" class="font-medium text-sm">Password</label>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    placeholder="Password"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <label for="password_confirmation" class="font-medium text-sm">Confirm Password</label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                label="Create account"
                :icon="processing ? 'pi pi-spin pi-spinner' : 'pi pi-user-plus'"
                class="mt-4 w-full"
                :tabindex="5"
                :loading="processing"
                data-test="register-button"
            />
        </div>

        <div class="text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account?
            <TextLink :href="login()" :tabindex="6">Log in</TextLink>
        </div>
    </Form>
</template>

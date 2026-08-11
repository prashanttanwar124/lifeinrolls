<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';

const showDialog = ref(false);
</script>

<template>
    <div class="space-y-6">
        <Heading
            variant="small"
            title="Delete account"
            description="Delete your account and all of its resources"
        />
        <div
            class="space-y-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/30 dark:bg-red-950/20"
        >
            <div class="relative space-y-0.5 text-red-700 dark:text-red-300">
                <p class="font-medium">Warning</p>
                <p class="text-sm">
                    Please proceed with caution, this cannot be undone.
                </p>
            </div>

            <Button
                label="Delete account"
                severity="danger"
                icon="pi pi-trash"
                @click="showDialog = true"
                data-test="delete-user-button"
            />

            <Dialog
                v-model:visible="showDialog"
                modal
                header="Are you sure you want to delete your account?"
                :style="{ width: '30rem' }"
            >
                <Form
                    v-bind="ProfileController.destroy.form()"
                    reset-on-success
                    :options="{ preserveScroll: true }"
                    class="space-y-6"
                    v-slot="{ errors, processing, reset, clearErrors }"
                >
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                    </p>

                    <div class="grid gap-2">
                        <label for="password" class="sr-only">Password</label>
                        <PasswordInput
                            id="password"
                            name="password"
                            placeholder="Password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <Button
                            label="Cancel"
                            severity="secondary"
                            text
                            @click="
                                () => {
                                    clearErrors();
                                    reset();
                                    showDialog = false;
                                }
                            "
                        />
                        <Button
                            type="submit"
                            label="Delete account"
                            severity="danger"
                            :icon="processing ? 'pi pi-spin pi-spinner' : 'pi pi-trash'"
                            :loading="processing"
                            data-test="confirm-delete-user-button"
                        />
                    </div>
                </Form>
            </Dialog>
        </div>
    </div>
</template>

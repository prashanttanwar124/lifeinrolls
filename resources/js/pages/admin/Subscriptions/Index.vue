<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { ref } from 'vue';

defineProps<{
    plans: any[];
}>();

const showDialog = ref(false);

const intervalOptions = [
    { label: 'Monthly', value: 'monthly' },
    { label: 'Yearly', value: 'yearly' },
];

const form = useForm({
    name: '',
    price: 4.99,
    interval: 'monthly',
    max_rolls: 10,
    max_photos_per_roll: 36,
    allows_custom_presets: true,
});

const submit = () => {
    form.post('/admin/subscriptions', {
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Subscription Plans" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Subscription Plans & Billing</h1>
                <p class="text-sm text-slate-500">Configure tier plans and limits for mobile app customers.</p>
            </div>
            <Button label="New Plan" icon="pi pi-plus" severity="emerald" @click="showDialog = true" />
        </div>

        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #content>
                <DataTable :value="plans" responsiveLayout="scroll">
                    <Column field="name" header="Plan Name"></Column>
                    <Column field="price" header="Price">
                        <template #body="slotProps">
                            <span class="font-bold">${{ slotProps.data.price }} / {{ slotProps.data.interval }}</span>
                        </template>
                    </Column>
                    <Column field="max_rolls" header="Max Rolls"></Column>
                    <Column field="max_photos_per_roll" header="Photos/Roll"></Column>
                    <Column field="allows_custom_presets" header="Custom Presets">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.allows_custom_presets ? 'Allowed' : 'Disabled'"
                                :severity="slotProps.data.allows_custom_presets ? 'success' : 'secondary'"
                            />
                        </template>
                    </Column>
                    <Column field="is_active" header="Status">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.is_active ? 'Active' : 'Disabled'"
                                :severity="slotProps.data.is_active ? 'success' : 'danger'"
                            />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- New Plan Dialog -->
        <Dialog v-model:visible="showDialog" header="Create Subscription Plan" modal :style="{ width: '30rem' }">
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <label for="name" class="text-sm font-medium">Plan Name</label>
                    <InputText id="name" v-model="form.name" required placeholder="e.g. Pro Creator" class="w-full" />
                </div>

                <div class="grid gap-2">
                    <label for="price" class="text-sm font-medium">Price ($)</label>
                    <InputNumber id="price" v-model="form.price" mode="currency" currency="USD" locale="en-US" class="w-full" />
                </div>

                <div class="grid gap-2">
                    <label for="interval" class="text-sm font-medium">Billing Interval</label>
                    <Select id="interval" v-model="form.interval" :options="intervalOptions" optionLabel="label" optionValue="value" class="w-full" />
                </div>

                <div class="grid gap-2">
                    <label for="max_rolls" class="text-sm font-medium">Max Film Rolls / Month</label>
                    <InputNumber id="max_rolls" v-model="form.max_rolls" :min="1" class="w-full" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button label="Cancel" severity="secondary" text @click="showDialog = false" />
                    <Button type="submit" label="Save Plan" severity="emerald" :loading="form.processing" />
                </div>
            </form>
        </Dialog>
    </div>
</template>

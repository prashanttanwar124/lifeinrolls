<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import ToggleSwitch from 'primevue/toggleswitch';
import { ref } from 'vue';

defineProps<{
    presets: any[];
}>();

const showDialog = ref(false);

const aspectRatioOptions = [
    { label: '4:3', value: '4:3' },
    { label: '3:2', value: '3:2' },
    { label: '16:9', value: '16:9' },
    { label: '1:1', value: '1:1' },
];

const form = useForm({
    preset_key: '',
    name: '',
    version: 1,
    engine: 'film_v1',
    lut: 'cameras/summer-35.cube',
    lut_file: null as File | null,
    grain: 0.35,
    bloom: 0.20,
    vignette: 0.15,
    aspect_ratio: '4:3',
    description: '',
    is_premium: true,
});

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.lut_file = target.files[0];
    }
};

const submit = () => {
    form.post('/admin/presets', {
        forceFormData: true,
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
        },
    });
};

const togglePreset = (id: number) => {
    router.post(`/admin/presets/${id}/toggle`, {}, { preserveScroll: true });
};

const deletePreset = (id: number) => {
    if (confirm('Delete this camera preset profile?')) {
        router.delete(`/admin/presets/${id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Camera Presets Management" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Camera Presets & LUT Storage</h1>
                <p class="text-sm text-slate-500">Manage camera profiles, upload 3D .cube LUT files, and configure mobile rendering parameters.</p>
            </div>
            <Button label="New Camera Profile" icon="pi pi-plus" severity="emerald" @click="showDialog = true" />
        </div>

        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #content>
                <DataTable :value="presets" responsiveLayout="scroll">
                    <Column field="preset_key" header="Key ID">
                        <template #body="slotProps">
                            <code class="px-2 py-1 bg-slate-100 dark:bg-zinc-800 rounded font-mono text-xs">{{ slotProps.data.preset_key }}</code>
                        </template>
                    </Column>
                    <Column field="name" header="Name"></Column>
                    <Column field="version" header="Ver">
                        <template #body="slotProps">
                            <span class="text-xs font-mono">v{{ slotProps.data.version }}</span>
                        </template>
                    </Column>
                    <Column field="lut_url" header="3D .cube LUT File">
                        <template #body="slotProps">
                            <a
                                v-if="slotProps.data.lut_url"
                                :href="slotProps.data.lut_url"
                                target="_blank"
                                class="inline-flex items-center gap-1 text-xs font-mono text-emerald-600 dark:text-emerald-400 underline"
                            >
                                <i class="pi pi-download text-xs"></i>
                                {{ slotProps.data.lut }}
                            </a>
                            <span v-else class="text-xs font-mono text-slate-400">{{ slotProps.data.lut }}</span>
                        </template>
                    </Column>
                    <Column field="grain" header="Grain">
                        <template #body="slotProps">
                            <span class="text-xs font-mono">{{ slotProps.data.grain }}</span>
                        </template>
                    </Column>
                    <Column field="bloom" header="Bloom">
                        <template #body="slotProps">
                            <span class="text-xs font-mono">{{ slotProps.data.bloom }}</span>
                        </template>
                    </Column>
                    <Column field="vignette" header="Vignette">
                        <template #body="slotProps">
                            <span class="text-xs font-mono">{{ slotProps.data.vignette }}</span>
                        </template>
                    </Column>
                    <Column field="aspect_ratio" header="Aspect"></Column>
                    <Column field="is_premium" header="Tier">
                        <template #body="slotProps">
                            <Tag
                                :value="slotProps.data.is_premium ? 'PRO' : 'FREE'"
                                :severity="slotProps.data.is_premium ? 'warn' : 'info'"
                            />
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <Button
                                    :label="slotProps.data.is_active ? 'Disable' : 'Enable'"
                                    severity="secondary"
                                    text
                                    size="small"
                                    @click="togglePreset(slotProps.data.id)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    severity="danger"
                                    text
                                    rounded
                                    size="small"
                                    @click="deletePreset(slotProps.data.id)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- New Camera Profile Dialog -->
        <Dialog v-model:visible="showDialog" header="Create Camera Profile & LUT" modal :style="{ width: '36rem' }">
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <label for="preset_key" class="text-sm font-medium">Key ID (id)</label>
                        <InputText id="preset_key" v-model="form.preset_key" required placeholder="e.g. summer_35" class="w-full" />
                    </div>
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium">Profile Name</label>
                        <InputText id="name" v-model="form.name" required placeholder="e.g. Summer 35" class="w-full" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <label for="engine" class="text-sm font-medium">Engine</label>
                        <InputText id="engine" v-model="form.engine" required placeholder="film_v1" class="w-full" />
                    </div>
                    <div class="grid gap-2">
                        <label for="lut" class="text-sm font-medium">LUT Path / Storage</label>
                        <InputText id="lut" v-model="form.lut" placeholder="cameras/summer-35.cube" class="w-full" />
                    </div>
                </div>

                <!-- File Upload for .cube -->
                <div class="grid gap-2">
                    <label class="text-sm font-medium">Upload .cube LUT File (Optional)</label>
                    <input
                        type="file"
                        accept=".cube"
                        class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-zinc-800 dark:file:text-emerald-400"
                        @change="handleFileUpload"
                    />
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="grid gap-2">
                        <label for="grain" class="text-sm font-medium">Grain (0.00-1.00)</label>
                        <InputNumber id="grain" v-model="form.grain" :min="0" :max="1" :minFractionDigits="2" :maxFractionDigits="2" :step="0.05" class="w-full" />
                    </div>
                    <div class="grid gap-2">
                        <label for="bloom" class="text-sm font-medium">Bloom (0.00-1.00)</label>
                        <InputNumber id="bloom" v-model="form.bloom" :min="0" :max="1" :minFractionDigits="2" :maxFractionDigits="2" :step="0.05" class="w-full" />
                    </div>
                    <div class="grid gap-2">
                        <label for="vignette" class="text-sm font-medium">Vignette (0.00-1.00)</label>
                        <InputNumber id="vignette" v-model="form.vignette" :min="0" :max="1" :minFractionDigits="2" :maxFractionDigits="2" :step="0.05" class="w-full" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <label for="aspect_ratio" class="text-sm font-medium">Aspect Ratio</label>
                        <Select id="aspect_ratio" v-model="form.aspect_ratio" :options="aspectRatioOptions" optionLabel="label" optionValue="value" class="w-full" />
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <ToggleSwitch v-model="form.is_premium" />
                        <label class="text-sm font-medium">Premium Profile</label>
                    </div>
                </div>

                <div class="grid gap-2">
                    <label for="description" class="text-sm font-medium">Description</label>
                    <Textarea id="description" v-model="form.description" rows="2" class="w-full" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button label="Cancel" severity="secondary" text @click="showDialog = false" />
                    <Button type="submit" label="Save Profile & LUT" severity="emerald" :loading="form.processing" />
                </div>
            </form>
        </Dialog>
    </div>
</template>

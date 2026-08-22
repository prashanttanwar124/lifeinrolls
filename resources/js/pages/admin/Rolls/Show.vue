<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Message from 'primevue/message';
import { ref } from 'vue';

const props = defineProps<{
    roll: any;
    members: any[];
    photos: any[];
    errors?: Record<string, string>;
}>();

const showUploadDialog = ref(false);
const previewUrl = ref<string | null>(null);

const form = useForm({
    photo: null as File | null,
    caption: '',
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.photo = target.files[0];
        previewUrl.value = URL.createObjectURL(target.files[0]);
    }
};

const submitUpload = () => {
    form.post(`/admin/rolls/${props.roll.id}/photos`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showUploadDialog.value = false;
            form.reset();
            previewUrl.value = null;
        },
    });
};

const photoStatusSeverity = (status: string) => {
    if (status === 'approved') return 'success';
    if (status === 'pending_approval') return 'warn';
    return 'danger';
};

const deletePhoto = (photoId: number) => {
    if (confirm('Delete this photo? The image file will be removed from storage.')) {
        router.delete(`/admin/rolls/${props.roll.id}/photos/${photoId}`, { preserveScroll: true });
    }
};

const formatDate = (value?: string) => (value ? new Date(value).toLocaleString() : '—');
</script>

<template>
    <Head :title="`Roll — ${roll.title}`" />

    <div class="flex flex-col gap-6">
        <!-- Error Banner if upload failed -->
        <div v-if="$page.props.errors?.upload_error || $page.props.errors?.photo" class="p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300">
            <div class="flex items-start gap-3">
                <i class="pi pi-exclamation-triangle text-xl mt-0.5 text-red-500"></i>
                <div class="space-y-1 text-sm font-medium">
                    <p class="font-bold text-red-800 dark:text-red-200">Storage / Upload Error Detected:</p>
                    <p class="font-mono text-xs break-all">{{ $page.props.errors.upload_error || $page.props.errors.photo }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <Link href="/admin/rolls">
                        <Button icon="pi pi-arrow-left" text rounded size="small" />
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight">{{ roll.title }}</h1>
                    <Tag :value="roll.roll_type" severity="secondary" class="capitalize" />
                    <Tag :value="roll.status" :severity="roll.status === 'active' ? 'success' : 'warn'" class="capitalize" />
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Created by {{ roll.creator?.name ?? 'Unknown' }} ·
                    {{ roll.memberships_count }} members ·
                    {{ roll.current_photos }} / {{ roll.max_photos }} exposures ·
                    Invite code
                    <code class="px-1.5 py-0.5 bg-slate-100 dark:bg-zinc-800 rounded font-mono text-xs">{{ roll.invite_code }}</code>
                </p>
                <p v-if="roll.description" class="text-sm text-slate-500 mt-1">{{ roll.description }}</p>
            </div>

            <Button
                label="Upload Test Photo to Roll"
                icon="pi pi-upload"
                severity="primary"
                @click="showUploadDialog = true"
            />
        </div>

        <!-- Upload Modal -->
        <Dialog
            v-model:visible="showUploadDialog"
            modal
            header="Upload Photo to Roll (R2 / Storage Test)"
            :style="{ width: '480px' }"
        >
            <form @submit.prevent="submitUpload" class="flex flex-col gap-4 pt-2">
                <div v-if="form.errors.upload_error" class="p-3 bg-red-50 border border-red-200 rounded text-red-700 text-xs font-mono break-all">
                    {{ form.errors.upload_error }}
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold">Select Image File</label>
                    <input
                        type="file"
                        accept="image/*"
                        required
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"
                        @change="handleFileChange"
                    />
                    <small v-if="form.errors.photo" class="text-red-500">{{ form.errors.photo }}</small>
                </div>

                <div v-if="previewUrl" class="rounded-lg overflow-hidden border border-slate-200 dark:border-zinc-800 bg-black flex justify-center">
                    <img :src="previewUrl" alt="Upload preview" class="max-h-48 object-contain" />
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold">Caption (Optional)</label>
                    <InputText v-model="form.caption" placeholder="e.g. Test exposure from admin dashboard" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button label="Cancel" severity="secondary" text @click="showUploadDialog = false" />
                    <Button
                        type="submit"
                        label="Upload Now"
                        icon="pi pi-cloud-upload"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </Dialog>

        <!-- Photos -->
        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #title>
                <span class="text-base font-semibold">Photos ({{ photos.length }})</span>
            </template>
            <template #content>
                <p v-if="photos.length === 0" class="text-sm text-slate-500 py-6 text-center">
                    No photos uploaded to this roll yet.
                </p>
                <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    <div
                        v-for="photo in photos"
                        :key="photo.id"
                        class="rounded-lg overflow-hidden border border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900"
                    >
                        <a :href="photo.photo_url" target="_blank" rel="noopener">
                            <img
                                :src="photo.thumbnail_url || photo.photo_url"
                                :alt="photo.caption || `Photo ${photo.id}`"
                                class="w-full aspect-square object-cover"
                                loading="lazy"
                            />
                        </a>
                        <div class="p-2.5 flex flex-col gap-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold truncate">{{ photo.user?.name ?? 'Unknown' }}</span>
                                <Tag
                                    :value="photo.status === 'pending_approval' ? 'pending' : photo.status"
                                    :severity="photoStatusSeverity(photo.status)"
                                    class="capitalize !text-[10px]"
                                />
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] text-slate-500">{{ formatDate(photo.created_at) }}</span>
                                <Button
                                    icon="pi pi-trash"
                                    severity="danger"
                                    text
                                    rounded
                                    size="small"
                                    @click="deletePhoto(photo.id)"
                                />
                            </div>
                            <p v-if="photo.caption" class="text-[11px] text-slate-500 truncate">{{ photo.caption }}</p>
                        </div>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Members -->
        <Card class="border border-slate-200 dark:border-zinc-800 shadow-sm">
            <template #title>
                <span class="text-base font-semibold">Members ({{ members.length }})</span>
            </template>
            <template #content>
                <DataTable :value="members" responsiveLayout="scroll">
                    <Column field="user.name" header="Name"></Column>
                    <Column field="user.email" header="Email"></Column>
                    <Column field="role" header="Role">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.role" severity="secondary" class="capitalize" />
                        </template>
                    </Column>
                    <Column field="joined_at" header="Joined">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.joined_at) }}
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </div>
</template>

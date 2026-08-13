<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Tag from 'primevue/tag';

const props = defineProps<{
    roll: any;
    members: any[];
    photos: any[];
}>();

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
        </div>

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

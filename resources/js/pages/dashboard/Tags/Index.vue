<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, destroy } from '@/routes/admin/tags';
import { type BreadcrumbItem } from '@/types';

type Tag = {
    id: string;
    name: string;
    slug: string;
    tools_count: number;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedTags = {
    data: Tag[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
};

defineProps<{
    tags: PaginatedTags;
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tags' },
];

const flash = ref(page.props.flash as { success?: string; error?: string });

watch(
    () => page.props.flash,
    (newFlash) => {
        flash.value = newFlash as { success?: string; error?: string };
    },
);

const deleteDialog = ref(false);
const tagToDelete = ref<Tag | null>(null);

function confirmDelete(tag: Tag) {
    tagToDelete.value = tag;
    deleteDialog.value = true;
}

function performDelete() {
    if (!tagToDelete.value) return;
    router.delete(destroy.url(tagToDelete.value.slug), {
        onFinish: () => {
            deleteDialog.value = false;
            tagToDelete.value = null;
        },
    });
}
</script>

<template>
    <Head title="Tags" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <Heading title="Tags" description="Gérer les tags d'outils" />
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 size-4" />
                        Ajouter un tag
                    </Link>
                </Button>
            </div>

            <div
                v-if="flash.success"
                class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200"
            >
                {{ flash.success }}
            </div>

            <div
                v-if="flash.error"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200"
            >
                {{ flash.error }}
            </div>

            <div
                class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border"
                    >
                        <tr>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="w-24 px-4 py-3 text-center">Outils</th>
                            <th class="w-28 px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="tag in tags.data"
                            :key="tag.id"
                            class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ tag.name }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ tag.slug }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ tag.tools_count }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link :href="edit.url(tag.slug)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="confirmDelete(tag)"
                                    >
                                        <Trash2 class="size-4 text-red-500" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="tags.data.length === 0">
                            <td
                                colspan="4"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Aucun tag trouvé.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="tags.last_page > 1"
                class="flex items-center justify-center gap-1"
            >
                <template v-for="link in tags.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        variant="outline"
                        size="sm"
                        as-child
                        :class="{
                            'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground':
                                link.active,
                        }"
                    >
                        <Link :href="link.url">
                            <span v-html="link.label" />
                        </Link>
                    </Button>
                    <Button v-else variant="outline" size="sm" disabled>
                        <span v-html="link.label" />
                    </Button>
                </template>
            </div>
        </div>

        <Dialog v-model:open="deleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Supprimer le tag</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer le tag «
                        {{ tagToDelete?.name }} » ? Cette action est
                        irréversible.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialog = false"
                        >Annuler</Button
                    >
                    <Button variant="destructive" @click="performDelete"
                        >Supprimer</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

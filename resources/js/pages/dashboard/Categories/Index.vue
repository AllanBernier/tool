<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, GripVertical, Pencil, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
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
import { type BreadcrumbItem } from '@/types';
import { index as categoriesIndex, create, edit, destroy, reorder } from '@/routes/admin/categories';

type Category = {
    id: string;
    name: string;
    slug: string;
    icon: string;
    description: string;
    sort_order: number;
    tools_count: number;
};

const props = defineProps<{
    categories: Category[];
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Catégories' },
];

const flash = ref(page.props.flash as { success?: string; error?: string });

watch(
    () => page.props.flash,
    (newFlash) => {
        flash.value = newFlash as { success?: string; error?: string };
    },
);

const deleteDialog = ref(false);
const categoryToDelete = ref<Category | null>(null);

function confirmDelete(category: Category) {
    categoryToDelete.value = category;
    deleteDialog.value = true;
}

function performDelete() {
    if (!categoryToDelete.value) return;
    router.delete(destroy.url(categoryToDelete.value.slug), {
        onFinish: () => {
            deleteDialog.value = false;
            categoryToDelete.value = null;
        },
    });
}

let dragIndex: number | null = null;

function onDragStart(index: number) {
    dragIndex = index;
}

function onDragOver(e: DragEvent) {
    e.preventDefault();
}

function onDrop(targetIndex: number) {
    if (dragIndex === null || dragIndex === targetIndex) return;

    const items = [...props.categories];
    const [moved] = items.splice(dragIndex, 1);
    items.splice(targetIndex, 0, moved);

    const ids = items.map((c) => c.id);
    router.post(reorder.url(), { ids }, { preserveScroll: true });

    dragIndex = null;
}
</script>

<template>
    <Head title="Catégories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <Heading title="Catégories" description="Gérer les catégories d'outils" />
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 size-4" />
                        Ajouter une catégorie
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

            <div class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                        <tr>
                            <th class="w-10 px-4 py-3"></th>
                            <th class="w-12 px-4 py-3">Icône</th>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="w-24 px-4 py-3 text-center">Outils</th>
                            <th class="w-28 px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(category, index) in categories"
                            :key="category.id"
                            class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                            draggable="true"
                            @dragstart="onDragStart(index)"
                            @dragover="onDragOver"
                            @drop="onDrop(index)"
                        >
                            <td class="px-4 py-3">
                                <GripVertical class="size-4 cursor-grab text-muted-foreground" />
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ category.icon }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ category.name }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ category.slug }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ category.tools_count }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="edit.url(category.slug)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="confirmDelete(category)"
                                    >
                                        <Trash2 class="size-4 text-red-500" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="categories.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                Aucune catégorie trouvée.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Dialog v-model:open="deleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Supprimer la catégorie</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer la catégorie
                        « {{ categoryToDelete?.name }} » ? Cette action est irréversible.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteDialog = false">Annuler</Button>
                    <Button variant="destructive" @click="performDelete">Supprimer</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

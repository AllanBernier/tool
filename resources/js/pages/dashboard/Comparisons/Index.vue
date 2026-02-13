<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
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
import { index as comparisonsIndex, create, edit, destroy } from '@/routes/admin/comparatifs';
import { togglePublish } from '@/routes/admin/comparisons';

type ToolInfo = {
    id: string;
    name: string;
    slug: string;
};

type ComparisonItem = {
    id: string;
    slug: string;
    is_published: boolean;
    generation_status: string;
    created_at: string;
    tool_a: ToolInfo | null;
    tool_b: ToolInfo | null;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedComparisons = {
    data: ComparisonItem[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
};

type Filters = {
    generation_status: string;
};

const props = defineProps<{
    comparisons: PaginatedComparisons;
    filters: Filters;
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Comparatifs' },
];

const flash = ref(page.props.flash as { success?: string; error?: string });

watch(
    () => page.props.flash,
    (newFlash) => {
        flash.value = newFlash as { success?: string; error?: string };
    },
);

const statusFilter = ref(props.filters.generation_status);

function applyFilters() {
    router.get(
        comparisonsIndex().url,
        {
            generation_status: statusFilter.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

const deleteDialog = ref(false);
const comparisonToDelete = ref<ComparisonItem | null>(null);

function confirmDelete(comparison: ComparisonItem) {
    comparisonToDelete.value = comparison;
    deleteDialog.value = true;
}

function performDelete() {
    if (!comparisonToDelete.value) return;
    router.delete(destroy.url(comparisonToDelete.value.slug), {
        onFinish: () => {
            deleteDialog.value = false;
            comparisonToDelete.value = null;
        },
    });
}

function doTogglePublish(comparison: ComparisonItem) {
    router.post(togglePublish.url(comparison.slug), {}, { preserveScroll: true });
}

function comparisonLabel(comparison: ComparisonItem): string {
    const a = comparison.tool_a?.name ?? '—';
    const b = comparison.tool_b?.name ?? '—';
    return `${a} vs ${b}`;
}

const generationStatusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    generating: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};

const generationStatusLabels: Record<string, string> = {
    pending: 'En attente',
    generating: 'Génération',
    completed: 'Terminé',
    failed: 'Échoué',
};
</script>

<template>
    <Head title="Comparatifs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <Heading title="Comparatifs" description="Gérer les comparaisons d'outils" />
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 size-4" />
                        Ajouter un comparatif
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

            <div class="flex flex-wrap items-center gap-3">
                <select
                    v-model="statusFilter"
                    class="border-input bg-background ring-offset-background focus-visible:ring-ring h-9 rounded-md border px-3 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="applyFilters"
                >
                    <option value="">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="generating">Génération</option>
                    <option value="completed">Terminé</option>
                    <option value="failed">Échoué</option>
                </select>
            </div>

            <div class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3">Comparaison</th>
                            <th class="w-28 px-4 py-3 text-center">Publication</th>
                            <th class="w-28 px-4 py-3 text-center">Génération</th>
                            <th class="w-32 px-4 py-3">Créé le</th>
                            <th class="w-36 px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="comparison in comparisons.data"
                            :key="comparison.id"
                            class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ comparisonLabel(comparison) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Badge
                                    :class="comparison.is_published
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                    variant="outline"
                                >
                                    {{ comparison.is_published ? 'Publié' : 'Brouillon' }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Badge
                                    :class="generationStatusColors[comparison.generation_status] ?? ''"
                                    variant="outline"
                                >
                                    {{ generationStatusLabels[comparison.generation_status] ?? comparison.generation_status }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ new Date(comparison.created_at).toLocaleDateString('fr-FR') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="icon" as-child>
                                        <Link :href="edit.url(comparison.slug)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="doTogglePublish(comparison)"
                                    >
                                        {{ comparison.is_published ? 'Dépublier' : 'Publier' }}
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="confirmDelete(comparison)"
                                    >
                                        <Trash2 class="size-4 text-red-500" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="comparisons.data.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                                Aucun comparatif trouvé.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="comparisons.last_page > 1" class="flex items-center justify-center gap-1">
                <template v-for="link in comparisons.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        variant="outline"
                        size="sm"
                        as-child
                        :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground': link.active }"
                    >
                        <Link :href="link.url" v-html="link.label" />
                    </Button>
                    <Button
                        v-else
                        variant="outline"
                        size="sm"
                        disabled
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>

        <Dialog v-model:open="deleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Supprimer le comparatif</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer le comparatif
                        « {{ comparisonToDelete ? comparisonLabel(comparisonToDelete) : '' }} » ? Cette action est irréversible.
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

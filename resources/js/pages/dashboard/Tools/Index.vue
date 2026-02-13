<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Eye, Image } from 'lucide-vue-next';
import { ref, watch } from 'vue';
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
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as toolsIndex,
    create,
    edit,
    show,
    destroy,
} from '@/routes/admin/outils';
import { togglePublish } from '@/routes/admin/tools';
import { type BreadcrumbItem } from '@/types';

type Category = {
    id: string;
    name: string;
};

type ToolItem = {
    id: string;
    name: string;
    slug: string;
    logo_url: string;
    is_published: boolean;
    generation_status: string;
    created_at: string;
    category: Category | null;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedTools = {
    data: ToolItem[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
};

type Filters = {
    search: string;
    category_id: string;
    generation_status: string;
};

const props = defineProps<{
    tools: PaginatedTools;
    categories: Category[];
    filters: Filters;
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Outils' },
];

const flash = ref(page.props.flash as { success?: string; error?: string });

watch(
    () => page.props.flash,
    (newFlash) => {
        flash.value = newFlash as { success?: string; error?: string };
    },
);

const search = ref(props.filters.search);
const categoryFilter = ref(props.filters.category_id);
const statusFilter = ref(props.filters.generation_status);

function applyFilters() {
    router.get(
        toolsIndex().url,
        {
            search: search.value || undefined,
            category_id: categoryFilter.value || undefined,
            generation_status: statusFilter.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

let searchTimeout: ReturnType<typeof setTimeout>;
function onSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
}

const deleteDialog = ref(false);
const toolToDelete = ref<ToolItem | null>(null);

function confirmDelete(tool: ToolItem) {
    toolToDelete.value = tool;
    deleteDialog.value = true;
}

function performDelete() {
    if (!toolToDelete.value) return;
    router.delete(destroy.url(toolToDelete.value.slug), {
        onFinish: () => {
            deleteDialog.value = false;
            toolToDelete.value = null;
        },
    });
}

function doTogglePublish(tool: ToolItem) {
    router.post(togglePublish.url(tool.slug), {}, { preserveScroll: true });
}

const generationStatusColors: Record<string, string> = {
    pending:
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    generating: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    completed:
        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
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
    <Head title="Outils" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <Heading
                    title="Outils"
                    description="Gérer les outils de développement"
                />
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 size-4" />
                        Ajouter un outil
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
                <Input
                    v-model="search"
                    placeholder="Rechercher par nom..."
                    class="w-64"
                    @input="onSearchInput"
                />
                <select
                    v-model="categoryFilter"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="applyFilters"
                >
                    <option value="">Toutes les catégories</option>
                    <option
                        v-for="cat in categories"
                        :key="cat.id"
                        :value="cat.id"
                    >
                        {{ cat.name }}
                    </option>
                </select>
                <select
                    v-model="statusFilter"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                    @change="applyFilters"
                >
                    <option value="">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="generating">Génération</option>
                    <option value="completed">Terminé</option>
                    <option value="failed">Échoué</option>
                </select>
            </div>

            <div
                class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border"
                    >
                        <tr>
                            <th class="w-12 px-4 py-3"></th>
                            <th class="px-4 py-3">Nom</th>
                            <th class="px-4 py-3">Catégorie</th>
                            <th class="w-28 px-4 py-3 text-center">
                                Publication
                            </th>
                            <th class="w-28 px-4 py-3 text-center">
                                Génération
                            </th>
                            <th class="w-32 px-4 py-3">Créé le</th>
                            <th class="w-36 px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="tool in tools.data"
                            :key="tool.id"
                            class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3">
                                <img
                                    v-if="tool.logo_url"
                                    :src="tool.logo_url"
                                    :alt="tool.name"
                                    class="size-8 rounded object-contain"
                                />
                                <div
                                    v-else
                                    class="flex size-8 items-center justify-center rounded bg-muted"
                                >
                                    <Image
                                        class="size-4 text-muted-foreground"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ tool.name }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ tool.category?.name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Badge
                                    :class="
                                        tool.is_published
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
                                    "
                                    variant="outline"
                                >
                                    {{
                                        tool.is_published
                                            ? 'Publié'
                                            : 'Brouillon'
                                    }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Badge
                                    :class="
                                        generationStatusColors[
                                            tool.generation_status
                                        ] ?? ''
                                    "
                                    variant="outline"
                                >
                                    {{
                                        generationStatusLabels[
                                            tool.generation_status
                                        ] ?? tool.generation_status
                                    }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{
                                    new Date(
                                        tool.created_at,
                                    ).toLocaleDateString('fr-FR')
                                }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link :href="show.url(tool.slug)">
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link :href="edit.url(tool.slug)">
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="doTogglePublish(tool)"
                                    >
                                        {{
                                            tool.is_published
                                                ? 'Dépublier'
                                                : 'Publier'
                                        }}
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="confirmDelete(tool)"
                                    >
                                        <Trash2 class="size-4 text-red-500" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="tools.data.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Aucun outil trouvé.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="tools.last_page > 1"
                class="flex items-center justify-center gap-1"
            >
                <template v-for="link in tools.links" :key="link.label">
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
                    <DialogTitle>Supprimer l'outil</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer l'outil «
                        {{ toolToDelete?.name }} » ? Cette action est
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

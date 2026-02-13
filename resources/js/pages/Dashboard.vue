<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Wrench, GitCompareArrows, FolderTree, Tags, Image, Loader2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as toolsIndex } from '@/routes/admin/outils';
import { index as categoriesIndex } from '@/routes/admin/categories';
import { index as tagsIndex } from '@/routes/admin/tags';
import { index as comparisonsIndex } from '@/routes/admin/comparatifs';
import { edit as editTool } from '@/routes/admin/outils';
import { edit as editComparison } from '@/routes/admin/comparatifs';

type Stats = {
    tools: number;
    tools_published: number;
    comparisons: number;
    categories: number;
    tags: number;
};

type ToolItem = {
    id: string;
    name: string;
    slug: string;
    logo_url: string;
    is_published: boolean;
    generation_status: string;
    category: { id: string; name: string } | null;
    created_at: string;
};

type ComparisonItem = {
    id: string;
    slug: string;
    generation_status: string;
    is_published: boolean;
    tool_a: { id: string; name: string; slug: string } | null;
    tool_b: { id: string; name: string; slug: string } | null;
    created_at: string;
};

const props = defineProps<{
    stats: Stats;
    latestTools: ToolItem[];
    latestComparisons: ComparisonItem[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <Heading title="Dashboard" description="Vue d'ensemble de votre site" />

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Link :href="toolsIndex().url">
                    <Card class="transition-colors hover:border-primary/50">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-muted-foreground">Outils</CardTitle>
                            <Wrench class="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ stats.tools }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ stats.tools_published }} publié{{ stats.tools_published > 1 ? 's' : '' }}
                            </p>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="comparisonsIndex().url">
                    <Card class="transition-colors hover:border-primary/50">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-muted-foreground">Comparatifs</CardTitle>
                            <GitCompareArrows class="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ stats.comparisons }}</div>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="categoriesIndex().url">
                    <Card class="transition-colors hover:border-primary/50">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-muted-foreground">Catégories</CardTitle>
                            <FolderTree class="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ stats.categories }}</div>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="tagsIndex().url">
                    <Card class="transition-colors hover:border-primary/50">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-muted-foreground">Tags</CardTitle>
                            <Tags class="size-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ stats.tags }}</div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="flex flex-col gap-4">
                    <h2 class="text-lg font-semibold">Derniers outils ajoutés</h2>
                    <div class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                                <tr>
                                    <th class="w-10 px-4 py-3"></th>
                                    <th class="px-4 py-3">Nom</th>
                                    <th class="w-28 px-4 py-3 text-center">Statut</th>
                                    <th class="w-28 px-4 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="tool in latestTools"
                                    :key="tool.id"
                                    class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                                >
                                    <td class="px-4 py-3">
                                        <img
                                            v-if="tool.logo_url"
                                            :src="tool.logo_url"
                                            :alt="tool.name"
                                            class="size-7 rounded object-contain"
                                        />
                                        <div v-else class="flex size-7 items-center justify-center rounded bg-muted">
                                            <Image class="size-3.5 text-muted-foreground" />
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <Link :href="editTool.url(tool.slug)" class="font-medium hover:underline">
                                            {{ tool.name }}
                                        </Link>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <Badge
                                            :class="generationStatusColors[tool.generation_status] ?? ''"
                                            variant="outline"
                                        >
                                            <Loader2 v-if="tool.generation_status === 'generating'" class="mr-1 size-3 animate-spin" />
                                            {{ generationStatusLabels[tool.generation_status] ?? tool.generation_status }}
                                        </Badge>
                                    </td>
                                    <td class="px-4 py-3 text-muted-foreground">
                                        {{ new Date(tool.created_at).toLocaleDateString('fr-FR') }}
                                    </td>
                                </tr>
                                <tr v-if="latestTools.length === 0">
                                    <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                                        Aucun outil pour le moment.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <h2 class="text-lg font-semibold">Derniers comparatifs</h2>
                    <div class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                                <tr>
                                    <th class="px-4 py-3">Comparaison</th>
                                    <th class="w-28 px-4 py-3 text-center">Statut</th>
                                    <th class="w-28 px-4 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="comparison in latestComparisons"
                                    :key="comparison.id"
                                    class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                                >
                                    <td class="px-4 py-3">
                                        <Link :href="editComparison.url(comparison.slug)" class="font-medium hover:underline">
                                            {{ comparison.tool_a?.name ?? '—' }} vs {{ comparison.tool_b?.name ?? '—' }}
                                        </Link>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <Badge
                                            :class="generationStatusColors[comparison.generation_status] ?? ''"
                                            variant="outline"
                                        >
                                            <Loader2 v-if="comparison.generation_status === 'generating'" class="mr-1 size-3 animate-spin" />
                                            {{ generationStatusLabels[comparison.generation_status] ?? comparison.generation_status }}
                                        </Badge>
                                    </td>
                                    <td class="px-4 py-3 text-muted-foreground">
                                        {{ new Date(comparison.created_at).toLocaleDateString('fr-FR') }}
                                    </td>
                                </tr>
                                <tr v-if="latestComparisons.length === 0">
                                    <td colspan="3" class="px-4 py-8 text-center text-muted-foreground">
                                        Aucun comparatif pour le moment.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

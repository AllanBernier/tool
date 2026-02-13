<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, Image } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { index as toolsIndex, edit } from '@/routes/admin/outils';

type Category = {
    id: string;
    name: string;
};

type Tag = {
    id: string;
    name: string;
};

type ToolData = {
    id: string;
    name: string;
    slug: string;
    url: string;
    logo_url: string;
    logo_path: string | null;
    description: string | null;
    content: string | null;
    is_published: boolean;
    generation_status: string;
    category: Category | null;
    tags: Tag[];
    created_at: string;
};

const props = defineProps<{
    tool: ToolData;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Outils', href: toolsIndex().url },
    { title: props.tool.name },
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
    <Head :title="tool.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl p-4">
            <div class="mb-6 flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <img
                        v-if="tool.logo_path"
                        :src="tool.logo_url"
                        :alt="tool.name"
                        class="size-16 rounded-lg object-contain"
                    />
                    <div v-else class="flex size-16 items-center justify-center rounded-lg bg-muted">
                        <Image class="size-8 text-muted-foreground" />
                    </div>
                    <div>
                        <Heading :title="tool.name" :description="tool.url" />
                        <div class="mt-2 flex items-center gap-2">
                            <Badge
                                :class="tool.is_published
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                variant="outline"
                            >
                                {{ tool.is_published ? 'Publié' : 'Brouillon' }}
                            </Badge>
                            <Badge
                                :class="generationStatusColors[tool.generation_status] ?? ''"
                                variant="outline"
                            >
                                {{ generationStatusLabels[tool.generation_status] ?? tool.generation_status }}
                            </Badge>
                            <Badge v-if="tool.category" variant="secondary">
                                {{ tool.category.name }}
                            </Badge>
                        </div>
                    </div>
                </div>
                <Button as-child>
                    <Link :href="edit.url(tool.slug)">
                        <Pencil class="mr-2 size-4" />
                        Modifier
                    </Link>
                </Button>
            </div>

            <div v-if="tool.tags.length > 0" class="mb-6 flex flex-wrap gap-2">
                <Badge v-for="tag in tool.tags" :key="tag.id" variant="outline">
                    {{ tag.name }}
                </Badge>
            </div>

            <div v-if="tool.description" class="mb-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                <h3 class="mb-2 text-lg font-medium">Description</h3>
                <p class="text-muted-foreground">{{ tool.description }}</p>
            </div>

            <div v-if="tool.content" class="rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                <h3 class="mb-2 text-lg font-medium">Contenu</h3>
                <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap text-muted-foreground">{{ tool.content }}</div>
            </div>
        </div>
    </AppLayout>
</template>

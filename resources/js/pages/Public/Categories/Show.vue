<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { SlidersHorizontal } from 'lucide-vue-next';
import * as icons from 'lucide-vue-next';
import CategoryCard from '@/components/public/CategoryCard.vue';
import ToolCard from '@/components/public/ToolCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Category, Tag, Tool } from '@/types';
import { computed, ref, type Component, watch } from 'vue';

type PaginatedTools = {
    data: Tool[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

const props = defineProps<{
    category: Category;
    tools: PaginatedTools;
    tags: Pick<Tag, 'id' | 'name' | 'slug'>[];
    filters: {
        platform: string;
        tag: string;
        sort: string;
    };
}>();

const platform = ref(props.filters.platform);
const tag = ref(props.filters.tag);
const sort = ref(props.filters.sort);

function applyFilters(): void {
    router.get(
        `/categorie/${props.category.slug}`,
        {
            platform: platform.value || undefined,
            tag: tag.value || undefined,
            sort: sort.value !== 'recent' ? sort.value : undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
}

watch([platform, tag, sort], () => {
    applyFilters();
});

const platforms = [
    { value: '', label: 'Toutes les plateformes' },
    { value: 'web', label: 'Web' },
    { value: 'desktop', label: 'Desktop' },
    { value: 'mobile', label: 'Mobile' },
    { value: 'api', label: 'API' },
    { value: 'cli', label: 'CLI' },
];

const iconComponent = computed<Component | null>(() => {
    const iconName = props.category.icon;
    if (!iconName) return null;
    return (icons as Record<string, Component>)[iconName] ?? null;
});
</script>

<template>
    <PublicLayout>
        <Head :title="category.meta_title || category.name" />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-4">
                <div v-if="iconComponent" class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <component :is="iconComponent" class="size-7" />
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                        {{ category.name }}
                    </h1>
                    <p v-if="category.description" class="mt-1 text-lg text-muted-foreground">
                        {{ category.description }}
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-8 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
                <select
                    v-model="platform"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option v-for="p in platforms" :key="p.value" :value="p.value">
                        {{ p.label }}
                    </option>
                </select>

                <select
                    v-model="tag"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">
                        Tous les tags
                    </option>
                    <option v-for="t in tags" :key="t.id" :value="t.slug">
                        {{ t.name }}
                    </option>
                </select>

                <select
                    v-model="sort"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="recent">
                        Plus récents
                    </option>
                    <option value="alpha">
                        Alphabétique
                    </option>
                </select>
            </div>

            <!-- Tools Grid -->
            <div v-if="tools.data.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <ToolCard
                    v-for="tool in tools.data"
                    :key="tool.id"
                    :tool="tool"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-20">
                <SlidersHorizontal class="size-12 text-muted-foreground/40" />
                <h3 class="mt-4 text-lg font-semibold text-foreground">
                    Aucun outil trouvé
                </h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Aucun outil publié dans cette catégorie pour le moment.
                </p>
            </div>

            <!-- Pagination -->
            <nav v-if="tools.last_page > 1" class="mt-12 flex items-center justify-center gap-1">
                <template v-for="link in tools.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors"
                        :class="link.active
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-border bg-background text-foreground hover:bg-accent'"
                        v-html="link.label"
                        preserve-state
                    />
                    <span
                        v-else
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-md px-3 text-sm text-muted-foreground"
                        v-html="link.label"
                    />
                </template>
            </nav>
        </div>
    </PublicLayout>
</template>

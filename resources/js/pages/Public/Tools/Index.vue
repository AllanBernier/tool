<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Search, SlidersHorizontal } from 'lucide-vue-next';
import AppHead from '@/components/AppHead.vue';
import type { SeoMeta } from '@/components/AppHead.vue';
import ToolCard from '@/components/public/ToolCard.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Category, Tool } from '@/types';
import { ref, watch } from 'vue';

type PaginatedTools = {
    data: Tool[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

const props = defineProps<{
    seo: SeoMeta;
    tools: PaginatedTools;
    categories: Pick<Category, 'id' | 'name' | 'slug'>[];
    filters: {
        search: string;
        category: string;
        platform: string;
        sort: string;
    };
}>();

const search = ref(props.filters.search);
const category = ref(props.filters.category);
const platform = ref(props.filters.platform);
const sort = ref(props.filters.sort);

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters(): void {
    router.get(
        '/outils',
        {
            search: search.value || undefined,
            category: category.value || undefined,
            platform: platform.value || undefined,
            sort: sort.value !== 'recent' ? sort.value : undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch([category, platform, sort], () => {
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
</script>

<template>
    <PublicLayout>
        <AppHead :seo="seo" />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                    Tous les outils
                </h1>
                <p class="mt-2 text-lg text-muted-foreground">
                    Découvrez et comparez les meilleurs outils de développement.
                </p>
            </div>

            <!-- Filters -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Rechercher un outil..."
                        class="pl-10"
                    />
                </div>

                <div class="grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
                    <select
                        v-model="category"
                        class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">
                            Toutes les catégories
                        </option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                            {{ cat.name }}
                        </option>
                    </select>

                    <select
                        v-model="platform"
                        class="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option v-for="p in platforms" :key="p.value" :value="p.value">
                            {{ p.label }}
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
                    Essayez de modifier vos filtres de recherche.
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

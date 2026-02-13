<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { SlidersHorizontal } from 'lucide-vue-next';
import AppHead from '@/components/AppHead.vue';
import type { SeoMeta } from '@/components/AppHead.vue';
import ComparisonCard from '@/components/public/ComparisonCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Comparison } from '@/types';

type PaginatedComparisons = {
    data: Comparison[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

defineProps<{
    seo: SeoMeta;
    comparisons: PaginatedComparisons;
}>();
</script>

<template>
    <PublicLayout>
        <AppHead :seo="seo" />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1
                    class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
                >
                    Tous les comparatifs
                </h1>
                <p class="mt-2 text-lg text-muted-foreground">
                    Comparez les outils de développement côte à côte pour faire
                    le meilleur choix.
                </p>
            </div>

            <!-- Comparisons Grid -->
            <div
                v-if="comparisons.data.length"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <ComparisonCard
                    v-for="comparison in comparisons.data"
                    :key="comparison.id"
                    :comparison="comparison"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-20">
                <SlidersHorizontal class="size-12 text-muted-foreground/40" />
                <h3 class="mt-4 text-lg font-semibold text-foreground">
                    Aucun comparatif disponible
                </h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Les comparatifs seront bientôt disponibles.
                </p>
            </div>

            <!-- Pagination -->
            <nav
                v-if="comparisons.last_page > 1"
                class="mt-12 flex items-center justify-center gap-1"
            >
                <template v-for="link in comparisons.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors"
                        :class="
                            link.active
                                ? 'border-primary bg-primary text-primary-foreground'
                                : 'border-border bg-background text-foreground hover:bg-accent'
                        "
                        preserve-state
                    >
                        <span v-html="link.label" />
                    </Link>
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

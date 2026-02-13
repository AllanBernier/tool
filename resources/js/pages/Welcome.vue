<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';
import AppHead from '@/components/AppHead.vue';
import type { SeoMeta } from '@/components/AppHead.vue';
import JsonLd from '@/components/JsonLd.vue';
import CategoryCard from '@/components/public/CategoryCard.vue';
import ComparisonCard from '@/components/public/ComparisonCard.vue';
import ScrollReveal from '@/components/public/ScrollReveal.vue';
import SponsorPlaceholder from '@/components/public/SponsorPlaceholder.vue';
import TagBadge from '@/components/public/TagBadge.vue';
import ToolCard from '@/components/public/ToolCard.vue';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Category, Comparison, Tag, Tool } from '@/types';

const props = defineProps<{
    seo: SeoMeta;
    popularTools: Tool[];
    trendingComparisons: Comparison[];
    categories: Category[];
    popularTags: Tag[];
}>();

const jsonLdSchemas = computed(() => [
    {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: 'Tool',
        url: props.seo.canonical,
        potentialAction: {
            '@type': 'SearchAction',
            target: {
                '@type': 'EntryPoint',
                urlTemplate: `${props.seo.canonical}search?q={search_term_string}`,
            },
            'query-input': 'required name=search_term_string',
        },
    },
    {
        '@context': 'https://schema.org',
        '@type': 'Organization',
        name: 'Tool',
        url: props.seo.canonical,
        logo: props.seo.ogImage,
    },
]);
</script>

<template>
    <PublicLayout>
        <AppHead :seo="seo" />
        <JsonLd :schema="jsonLdSchemas" />

        <!-- Hero Section -->
        <section class="relative overflow-hidden border-b border-border/60">
            <div
                class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-primary/5"
            />
            <div
                class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 sm:py-28 lg:px-8 lg:py-32"
            >
                <div class="mx-auto max-w-3xl text-center">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-muted/50 px-4 py-1.5 text-sm text-muted-foreground"
                    >
                        <Sparkles class="size-4" />
                        Propulsé par l'IA
                    </div>
                    <h1
                        class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl lg:text-6xl"
                    >
                        Découvrez les meilleurs outils pour
                        <span class="text-primary">développeurs</span>
                    </h1>
                    <p class="mt-6 text-lg text-muted-foreground sm:text-xl">
                        Trouvez, comparez et choisissez les outils de
                        développement parfaits pour vos projets grâce à nos
                        fiches détaillées et comparatifs générés par IA.
                    </p>
                    <div
                        class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
                    >
                        <Button as-child size="lg" class="gap-2">
                            <Link href="/outils">
                                Explorer les outils
                                <ArrowRight class="size-4" />
                            </Link>
                        </Button>
                        <Button as-child variant="outline" size="lg">
                            <Link href="/comparatifs">
                                Voir les comparatifs
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Tools Section -->
        <ScrollReveal>
            <section
                v-if="popularTools.length"
                class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8"
            >
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <h2
                            class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                        >
                            Outils populaires
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            Les outils les plus récents pour booster votre
                            productivité.
                        </p>
                    </div>
                    <Link
                        href="/outils"
                        class="hidden items-center gap-1 text-sm font-medium text-primary hover:underline sm:flex"
                    >
                        Voir tous les outils
                        <ArrowRight class="size-4" />
                    </Link>
                </div>
                <SponsorPlaceholder variant="banner" class="mb-8" />
                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <ToolCard
                        v-for="tool in popularTools"
                        :key="tool.id"
                        :tool="tool"
                    />
                </div>
                <div class="mt-8 text-center sm:hidden">
                    <Button as-child variant="outline">
                        <Link href="/outils"> Voir tous les outils </Link>
                    </Button>
                </div>
            </section>
        </ScrollReveal>

        <!-- Trending Comparisons Section -->
        <ScrollReveal>
            <section
                v-if="trendingComparisons.length"
                class="border-y border-border/60 bg-muted/30"
            >
                <div
                    class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8"
                >
                    <div class="mb-8 flex items-end justify-between">
                        <div>
                            <h2
                                class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                            >
                                Comparatifs tendance
                            </h2>
                            <p class="mt-2 text-muted-foreground">
                                Les comparaisons les plus consultées entre
                                outils populaires.
                            </p>
                        </div>
                        <Link
                            href="/comparatifs"
                            class="hidden items-center gap-1 text-sm font-medium text-primary hover:underline sm:flex"
                        >
                            Voir tous les comparatifs
                            <ArrowRight class="size-4" />
                        </Link>
                    </div>
                    <div
                        class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <ComparisonCard
                            v-for="comparison in trendingComparisons"
                            :key="comparison.id"
                            :comparison="comparison"
                        />
                    </div>
                    <div class="mt-8 text-center sm:hidden">
                        <Button as-child variant="outline">
                            <Link href="/comparatifs">
                                Voir tous les comparatifs
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>
        </ScrollReveal>

        <!-- Categories Section -->
        <ScrollReveal>
            <section
                v-if="categories.length"
                class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8"
            >
                <div class="mb-8">
                    <h2
                        class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                    >
                        Catégories
                    </h2>
                    <p class="mt-2 text-muted-foreground">
                        Parcourez les outils par catégorie pour trouver
                        exactement ce qu'il vous faut.
                    </p>
                </div>
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <CategoryCard
                        v-for="category in categories"
                        :key="category.id"
                        :category="category"
                    />
                </div>
            </section>
        </ScrollReveal>

        <!-- Popular Tags Section -->
        <ScrollReveal>
            <section
                v-if="popularTags.length"
                class="border-t border-border/60 bg-muted/30"
            >
                <div
                    class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8"
                >
                    <div class="mb-8">
                        <h2
                            class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl"
                        >
                            Tags populaires
                        </h2>
                        <p class="mt-2 text-muted-foreground">
                            Explorez les outils par technologie et domaine.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <TagBadge
                            v-for="tag in popularTags"
                            :key="tag.id"
                            :tag="tag"
                        />
                    </div>
                </div>
            </section>
        </ScrollReveal>

        <!-- Sponsor Placeholder Section -->
        <ScrollReveal>
            <section
                class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8"
            >
                <div
                    class="rounded-2xl border border-dashed border-border bg-muted/20 p-8 text-center sm:p-12"
                >
                    <Sparkles class="mx-auto size-8 text-muted-foreground/50" />
                    <h3 class="mt-4 text-lg font-semibold text-foreground">
                        Votre outil ici
                    </h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Mettez en avant votre outil de développement auprès de
                        milliers de développeurs.
                    </p>
                    <Button variant="outline" size="sm" class="mt-6" disabled>
                        Devenir sponsor
                    </Button>
                </div>
            </section>
        </ScrollReveal>
    </PublicLayout>
</template>

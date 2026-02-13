<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ExternalLink } from 'lucide-vue-next';
import MarkdownContent from '@/components/public/MarkdownContent.vue';
import PricingTable from '@/components/public/PricingTable.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Comparison, Tool } from '@/types';

const props = defineProps<{
    comparison: Comparison & {
        tool_a: Tool;
        tool_b: Tool;
    };
}>();

const sharedFeatures = (() => {
    const featuresA = props.comparison.tool_a.features ?? [];
    const featuresB = props.comparison.tool_b.features ?? [];
    if (!featuresA.length && !featuresB.length) {
        return [];
    }
    const allFeatures = [...new Set([...featuresA, ...featuresB])];
    return allFeatures.map((feature) => ({
        name: feature,
        toolA: featuresA.includes(feature),
        toolB: featuresB.includes(feature),
    }));
})();
</script>

<template>
    <PublicLayout>
        <Head :title="comparison.meta_title || `${comparison.tool_a.name} vs ${comparison.tool_b.name}`" />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <!-- Header -->
            <div class="mb-12 flex flex-col items-center gap-6 text-center">
                <div class="flex items-center gap-6 sm:gap-8">
                    <Link :href="`/outil/${comparison.tool_a.slug}`" class="flex flex-col items-center gap-3 transition-opacity hover:opacity-80">
                        <div class="flex size-16 items-center justify-center overflow-hidden rounded-2xl border border-border bg-muted/50 sm:size-20">
                            <img
                                :src="comparison.tool_a.logo_url"
                                :alt="`Logo ${comparison.tool_a.name}`"
                                class="size-12 object-contain sm:size-16"
                                loading="lazy"
                            />
                        </div>
                        <span class="text-lg font-semibold text-foreground sm:text-xl">
                            {{ comparison.tool_a.name }}
                        </span>
                    </Link>

                    <span class="shrink-0 text-2xl font-bold text-muted-foreground sm:text-3xl">VS</span>

                    <Link :href="`/outil/${comparison.tool_b.slug}`" class="flex flex-col items-center gap-3 transition-opacity hover:opacity-80">
                        <div class="flex size-16 items-center justify-center overflow-hidden rounded-2xl border border-border bg-muted/50 sm:size-20">
                            <img
                                :src="comparison.tool_b.logo_url"
                                :alt="`Logo ${comparison.tool_b.name}`"
                                class="size-12 object-contain sm:size-16"
                                loading="lazy"
                            />
                        </div>
                        <span class="text-lg font-semibold text-foreground sm:text-xl">
                            {{ comparison.tool_b.name }}
                        </span>
                    </Link>
                </div>

                <div class="flex flex-wrap justify-center gap-2">
                    <Link v-if="comparison.tool_a.category" :href="`/categorie/${comparison.tool_a.category.slug}`">
                        <Badge variant="secondary">{{ comparison.tool_a.category.name }}</Badge>
                    </Link>
                    <Link v-if="comparison.tool_b.category && comparison.tool_b.category.slug !== comparison.tool_a.category?.slug" :href="`/categorie/${comparison.tool_b.category.slug}`">
                        <Badge variant="secondary">{{ comparison.tool_b.category.name }}</Badge>
                    </Link>
                </div>
            </div>

            <!-- Feature Comparison Table -->
            <section v-if="sharedFeatures.length" class="mb-12">
                <h2 class="mb-4 text-xl font-semibold text-foreground">
                    Comparaison des fonctionnalités
                </h2>
                <div class="overflow-x-auto rounded-xl border border-border">
                    <table class="min-w-[500px] w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/50">
                                <th class="px-4 py-3 text-left font-semibold text-foreground">
                                    Fonctionnalité
                                </th>
                                <th class="px-4 py-3 text-center font-semibold text-foreground">
                                    {{ comparison.tool_a.name }}
                                </th>
                                <th class="px-4 py-3 text-center font-semibold text-foreground">
                                    {{ comparison.tool_b.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(feature, index) in sharedFeatures"
                                :key="index"
                                class="border-b border-border last:border-0"
                            >
                                <td class="px-4 py-3 text-foreground">
                                    {{ feature.name }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span v-if="feature.toolA" class="text-green-600 dark:text-green-400">&#10003;</span>
                                    <span v-else class="text-muted-foreground">&#10007;</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span v-if="feature.toolB" class="text-green-600 dark:text-green-400">&#10003;</span>
                                    <span v-else class="text-muted-foreground">&#10007;</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Content -->
            <section v-if="comparison.content" class="mb-12">
                <h2 class="mb-4 text-xl font-semibold text-foreground">
                    Analyse détaillée
                </h2>
                <MarkdownContent :content="comparison.content" />
            </section>

            <!-- Verdict -->
            <section v-if="comparison.verdict" class="mb-12">
                <div class="rounded-xl border border-primary/30 bg-primary/5 p-6">
                    <h2 class="mb-3 text-xl font-semibold text-foreground">
                        Verdict
                    </h2>
                    <p class="text-lg text-foreground">
                        {{ comparison.verdict }}
                    </p>
                </div>
            </section>

            <!-- Pricing Comparison -->
            <section v-if="comparison.tool_a.pricing?.length || comparison.tool_b.pricing?.length" class="mb-12">
                <h2 class="mb-4 text-xl font-semibold text-foreground">
                    Tarification comparée
                </h2>
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <div v-if="comparison.tool_a.pricing?.length">
                        <h3 class="mb-3 text-lg font-semibold text-foreground">
                            {{ comparison.tool_a.name }}
                        </h3>
                        <PricingTable :pricing="comparison.tool_a.pricing" />
                    </div>
                    <div v-if="comparison.tool_b.pricing?.length">
                        <h3 class="mb-3 text-lg font-semibold text-foreground">
                            {{ comparison.tool_b.name }}
                        </h3>
                        <PricingTable :pricing="comparison.tool_b.pricing" />
                    </div>
                </div>
            </section>

            <!-- Links to Tool Pages -->
            <section class="border-t border-border pt-12">
                <h2 class="mb-6 text-2xl font-bold tracking-tight text-foreground">
                    En savoir plus
                </h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <Link
                        :href="`/outil/${comparison.tool_a.slug}`"
                        class="flex items-center gap-4 rounded-xl border border-border bg-card p-5 shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
                    >
                        <div class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-border bg-muted/50">
                            <img
                                :src="comparison.tool_a.logo_url"
                                :alt="`Logo ${comparison.tool_a.name}`"
                                class="size-10 object-contain"
                                loading="lazy"
                            />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-foreground">
                                {{ comparison.tool_a.name }}
                            </h3>
                            <p v-if="comparison.tool_a.description" class="mt-1 line-clamp-1 text-sm text-muted-foreground">
                                {{ comparison.tool_a.description }}
                            </p>
                        </div>
                        <ExternalLink class="size-5 shrink-0 text-muted-foreground" />
                    </Link>

                    <Link
                        :href="`/outil/${comparison.tool_b.slug}`"
                        class="flex items-center gap-4 rounded-xl border border-border bg-card p-5 shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
                    >
                        <div class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-border bg-muted/50">
                            <img
                                :src="comparison.tool_b.logo_url"
                                :alt="`Logo ${comparison.tool_b.name}`"
                                class="size-10 object-contain"
                                loading="lazy"
                            />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-foreground">
                                {{ comparison.tool_b.name }}
                            </h3>
                            <p v-if="comparison.tool_b.description" class="mt-1 line-clamp-1 text-sm text-muted-foreground">
                                {{ comparison.tool_b.description }}
                            </p>
                        </div>
                        <ExternalLink class="size-5 shrink-0 text-muted-foreground" />
                    </Link>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>

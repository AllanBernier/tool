<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ExternalLink } from 'lucide-vue-next';
import AppHead from '@/components/AppHead.vue';
import type { SeoMeta } from '@/components/AppHead.vue';
import JsonLd from '@/components/JsonLd.vue';
import FaqAccordion from '@/components/public/FaqAccordion.vue';
import MarkdownContent from '@/components/public/MarkdownContent.vue';
import PlatformBadge from '@/components/public/PlatformBadge.vue';
import PricingTable from '@/components/public/PricingTable.vue';
import ProsCons from '@/components/public/ProsCons.vue';
import PublicBreadcrumbs from '@/components/public/PublicBreadcrumbs.vue';
import TagBadge from '@/components/public/TagBadge.vue';
import ToolCard from '@/components/public/ToolCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Comparison, Tool } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    seo: SeoMeta;
    tool: Tool & {
        alternatives: Tool[];
        comparisons_as_tool_a: (Comparison & { tool_b: Tool })[];
        comparisons_as_tool_b: (Comparison & { tool_a: Tool })[];
    };
}>();

const comparisons = [
    ...props.tool.comparisons_as_tool_a.map((c) => ({
        slug: c.slug,
        otherTool: c.tool_b,
    })),
    ...props.tool.comparisons_as_tool_b.map((c) => ({
        slug: c.slug,
        otherTool: c.tool_a,
    })),
];

const breadcrumbs = computed(() => [
    ...(props.tool.category
        ? [{ label: props.tool.category.name, href: `/categorie/${props.tool.category.slug}` }]
        : []),
    { label: props.tool.name },
]);

const jsonLdSchemas = computed(() => {
    const schemas: Record<string, unknown>[] = [];

    const softwareApp: Record<string, unknown> = {
        '@context': 'https://schema.org',
        '@type': 'SoftwareApplication',
        name: props.tool.name,
        url: props.seo.canonical,
        description: props.tool.description,
        applicationCategory: props.tool.category?.name ?? 'DeveloperApplication',
    };
    if (props.tool.platforms?.length) {
        softwareApp.operatingSystem = props.tool.platforms.join(', ');
    }
    if (props.tool.pricing?.length) {
        softwareApp.offers = props.tool.pricing.map((plan) => ({
            '@type': 'Offer',
            name: plan.name,
            price: plan.price,
            priceCurrency: 'USD',
        }));
    }
    schemas.push(softwareApp);

    if (props.tool.faq?.length) {
        schemas.push({
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            mainEntity: props.tool.faq.map((item) => ({
                '@type': 'Question',
                name: item.question,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: item.answer,
                },
            })),
        });
    }

    schemas.push({
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: [
            { '@type': 'ListItem', position: 1, name: 'Accueil', item: props.seo.canonical.replace(/\/outil\/.*/, '') },
            ...(props.tool.category
                ? [{
                    '@type': 'ListItem',
                    position: 2,
                    name: props.tool.category.name,
                    item: props.seo.canonical.replace(/\/outil\/.*/, `/categorie/${props.tool.category.slug}`),
                }]
                : []),
            {
                '@type': 'ListItem',
                position: props.tool.category ? 3 : 2,
                name: props.tool.name,
                item: props.seo.canonical,
            },
        ],
    });

    return schemas;
});
</script>

<template>
    <PublicLayout>
        <AppHead :seo="seo" />
        <JsonLd :schema="jsonLdSchemas" />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <PublicBreadcrumbs :items="breadcrumbs" />

            <!-- Header -->
            <div class="mb-12 flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                <div class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-border bg-muted/50 sm:size-24">
                    <img
                        :src="tool.logo_url"
                        :alt="`Logo ${tool.name}`"
                        class="size-16 object-contain sm:size-20"
                    />
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                            {{ tool.name }}
                        </h1>
                        <Link v-if="tool.category" :href="`/categorie/${tool.category.slug}`">
                            <Badge variant="secondary" class="text-sm">
                                {{ tool.category.name }}
                            </Badge>
                        </Link>
                    </div>
                    <p v-if="tool.description" class="mt-3 text-lg text-muted-foreground">
                        {{ tool.description }}
                    </p>
                    <div class="mt-4">
                        <Button v-if="tool.url" as-child size="sm" class="gap-2">
                            <a :href="tool.url" target="_blank" rel="noopener noreferrer">
                                Visiter le site
                                <ExternalLink class="size-4" />
                            </a>
                        </Button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="space-y-12 lg:col-span-2">
                    <!-- Description / Content -->
                    <section v-if="tool.content">
                        <h2 class="mb-4 text-xl font-semibold text-foreground">
                            Description
                        </h2>
                        <MarkdownContent :content="tool.content" />
                    </section>

                    <!-- Features -->
                    <section v-if="tool.features?.length">
                        <h2 class="mb-4 text-xl font-semibold text-foreground">
                            Fonctionnalités
                        </h2>
                        <ul class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <li
                                v-for="(feature, index) in tool.features"
                                :key="index"
                                class="flex items-start gap-2 text-sm text-foreground"
                            >
                                <span class="mt-1 size-1.5 shrink-0 rounded-full bg-primary" />
                                {{ feature }}
                            </li>
                        </ul>
                    </section>

                    <!-- Pros / Cons -->
                    <section v-if="tool.pros?.length || tool.cons?.length">
                        <h2 class="mb-4 text-xl font-semibold text-foreground">
                            Avantages et inconvénients
                        </h2>
                        <ProsCons :pros="tool.pros ?? []" :cons="tool.cons ?? []" />
                    </section>

                    <!-- Pricing -->
                    <section v-if="tool.pricing?.length">
                        <h2 class="mb-4 text-xl font-semibold text-foreground">
                            Tarification
                        </h2>
                        <PricingTable :pricing="tool.pricing" />
                    </section>

                    <!-- FAQ -->
                    <section v-if="tool.faq?.length">
                        <h2 class="mb-4 text-xl font-semibold text-foreground">
                            Questions fréquentes
                        </h2>
                        <FaqAccordion :faq="tool.faq" />
                    </section>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Platforms -->
                    <section v-if="tool.platforms?.length">
                        <h3 class="mb-3 text-sm font-semibold text-foreground">
                            Plateformes
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <PlatformBadge
                                v-for="platform in tool.platforms"
                                :key="platform"
                                :platform="platform"
                            />
                        </div>
                    </section>

                    <!-- Tags -->
                    <section v-if="tool.tags?.length">
                        <h3 class="mb-3 text-sm font-semibold text-foreground">
                            Tags
                        </h3>
                        <div class="flex flex-wrap gap-1.5">
                            <TagBadge
                                v-for="tag in tool.tags"
                                :key="tag.id"
                                :tag="tag"
                            />
                        </div>
                    </section>

                    <!-- Comparisons -->
                    <section v-if="comparisons.length">
                        <h3 class="mb-3 text-sm font-semibold text-foreground">
                            Comparatifs
                        </h3>
                        <div class="flex flex-col gap-2">
                            <Link
                                v-for="comparison in comparisons"
                                :key="comparison.slug"
                                :href="`/comparatif/${comparison.slug}`"
                                class="flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-sm text-foreground transition-colors hover:bg-muted/50"
                            >
                                <span class="font-medium">{{ tool.name }}</span>
                                <span class="text-muted-foreground">vs</span>
                                <span class="font-medium">{{ comparison.otherTool.name }}</span>
                            </Link>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Alternatives -->
            <section v-if="tool.alternatives?.length" class="mt-16 border-t border-border pt-12">
                <h2 class="mb-6 text-2xl font-bold tracking-tight text-foreground">
                    Alternatives à {{ tool.name }}
                </h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <ToolCard
                        v-for="alt in tool.alternatives"
                        :key="alt.id"
                        :tool="alt"
                    />
                </div>
            </section>
        </div>
    </PublicLayout>
</template>

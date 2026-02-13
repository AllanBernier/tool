<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { TagIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import AppHead from '@/components/AppHead.vue';
import type { SeoMeta } from '@/components/AppHead.vue';
import JsonLd from '@/components/JsonLd.vue';
import PublicBreadcrumbs from '@/components/public/PublicBreadcrumbs.vue';
import ToolCard from '@/components/public/ToolCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Tag, Tool } from '@/types';

type PaginatedTools = {
    data: Tool[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
};

const props = defineProps<{
    seo: SeoMeta;
    tag: Tag;
    tools: PaginatedTools;
}>();

const breadcrumbItems = [
    { label: 'Tags', href: '/outils' },
    { label: props.tag.name },
];

const jsonLdSchemas = computed(() => {
    const baseUrl = props.seo.canonical.replace(/\/tag\/.*/, '');
    return [
        {
            '@context': 'https://schema.org',
            '@type': 'CollectionPage',
            name: `Outils ${props.tag.name}`,
            url: props.seo.canonical,
            numberOfItems: props.tools.total,
        },
        {
            '@context': 'https://schema.org',
            '@type': 'BreadcrumbList',
            itemListElement: [
                {
                    '@type': 'ListItem',
                    position: 1,
                    name: 'Accueil',
                    item: baseUrl,
                },
                {
                    '@type': 'ListItem',
                    position: 2,
                    name: 'Tags',
                    item: `${baseUrl}/outils`,
                },
                {
                    '@type': 'ListItem',
                    position: 3,
                    name: props.tag.name,
                    item: props.seo.canonical,
                },
            ],
        },
    ];
});
</script>

<template>
    <PublicLayout>
        <AppHead :seo="seo" />
        <JsonLd :schema="jsonLdSchemas" />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <PublicBreadcrumbs :items="breadcrumbItems" />

            <!-- Header -->
            <div class="mb-8 flex items-center gap-4">
                <div
                    class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary"
                >
                    <TagIcon class="size-7" />
                </div>
                <div>
                    <h1
                        class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
                    >
                        {{ tag.name }}
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{
                            tools.total > 0
                                ? `${tools.total} outil${tools.total > 1 ? 's' : ''}`
                                : 'Aucun outil'
                        }}
                    </p>
                </div>
            </div>

            <!-- Tools Grid -->
            <div
                v-if="tools.data.length"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <ToolCard
                    v-for="tool in tools.data"
                    :key="tool.id"
                    :tool="tool"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-20">
                <TagIcon class="size-12 text-muted-foreground/40" />
                <h3 class="mt-4 text-lg font-semibold text-foreground">
                    Aucun outil trouvé
                </h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Aucun outil publié avec ce tag pour le moment.
                </p>
            </div>

            <!-- Pagination -->
            <nav
                v-if="tools.last_page > 1"
                class="mt-12 flex items-center justify-center gap-1"
            >
                <template v-for="link in tools.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors"
                        :class="
                            link.active
                                ? 'border-primary bg-primary text-primary-foreground'
                                : 'border-border bg-background text-foreground hover:bg-accent'
                        "
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

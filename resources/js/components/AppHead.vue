<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export type SeoMeta = {
    title: string;
    description: string;
    canonical: string;
    ogType: string;
    ogImage: string | null;
    paginationPrev?: string | null;
    paginationNext?: string | null;
};

const props = defineProps<{
    seo: SeoMeta;
}>();

const page = usePage();
const siteName = computed(() => (page.props.name as string) || 'Tool');
</script>

<template>
    <Head :title="seo.title">
        <meta head-key="description" name="description" :content="seo.description" />
        <link head-key="canonical" rel="canonical" :href="seo.canonical" />
        <meta head-key="og:title" property="og:title" :content="seo.title" />
        <meta head-key="og:description" property="og:description" :content="seo.description" />
        <meta head-key="og:url" property="og:url" :content="seo.canonical" />
        <meta head-key="og:type" property="og:type" :content="seo.ogType" />
        <meta head-key="og:site_name" property="og:site_name" :content="siteName" />
        <meta v-if="seo.ogImage" head-key="og:image" property="og:image" :content="seo.ogImage" />
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:title" name="twitter:title" :content="seo.title" />
        <meta head-key="twitter:description" name="twitter:description" :content="seo.description" />
        <meta v-if="seo.ogImage" head-key="twitter:image" name="twitter:image" :content="seo.ogImage" />
        <link v-if="seo.paginationPrev" head-key="prev" rel="prev" :href="seo.paginationPrev" />
        <link v-if="seo.paginationNext" head-key="next" rel="next" :href="seo.paginationNext" />
    </Head>
</template>

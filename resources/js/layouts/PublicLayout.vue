<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicFooter from '@/components/public/PublicFooter.vue';
import PublicHeader from '@/components/public/PublicHeader.vue';

type CategoryLink = {
    name: string;
    slug: string;
};

const page = usePage();

const categories = computed<CategoryLink[]>(
    () => (page.props.footerCategories as CategoryLink[]) ?? [],
);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <PublicHeader />
        <main class="flex-1">
            <slot />
        </main>
        <PublicFooter :categories="categories" />
    </div>
</template>

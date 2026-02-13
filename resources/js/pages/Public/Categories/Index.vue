<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CategoryCard from '@/components/public/CategoryCard.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Category } from '@/types';

defineProps<{
    categories: (Category & { tools_count: number })[];
}>();
</script>

<template>
    <PublicLayout>
        <Head title="Toutes les catégories" />

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                    Toutes les catégories
                </h1>
                <p class="mt-2 text-lg text-muted-foreground">
                    Explorez les outils de développement par catégorie.
                </p>
            </div>

            <!-- Categories Grid -->
            <div v-if="categories.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <CategoryCard
                    v-for="category in categories"
                    :key="category.id"
                    :category="category"
                />
            </div>
        </div>
    </PublicLayout>
</template>

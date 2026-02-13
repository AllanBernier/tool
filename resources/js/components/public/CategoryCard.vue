<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import * as icons from 'lucide-vue-next';
import { computed, type Component } from 'vue';
import type { Category } from '@/types';

const props = defineProps<{
    category: Category;
}>();

const iconComponent = computed<Component | null>(() => {
    const iconName = props.category.icon;
    if (!iconName) return null;
    return (icons as Record<string, Component>)[iconName] ?? null;
});
</script>

<template>
    <Link
        :href="`/categorie/${category.slug}`"
        class="group flex items-center gap-4 rounded-xl border border-border bg-card p-5 text-card-foreground shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
    >
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
            <component :is="iconComponent" v-if="iconComponent" class="size-5" />
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="truncate text-sm font-semibold text-foreground group-hover:text-primary">
                {{ category.name }}
            </h3>
            <p v-if="category.tools_count !== undefined" class="text-xs text-muted-foreground">
                {{ category.tools_count }} {{ category.tools_count === 1 ? 'outil' : 'outils' }}
            </p>
        </div>
    </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Home } from 'lucide-vue-next';

export type BreadcrumbItem = {
    label: string;
    href?: string;
};

defineProps<{
    items: BreadcrumbItem[];
}>();
</script>

<template>
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-muted-foreground">
            <li class="flex items-center gap-1.5">
                <Link href="/" class="flex items-center gap-1 transition-colors hover:text-foreground">
                    <Home class="size-3.5" />
                    <span>Accueil</span>
                </Link>
            </li>
            <li v-for="(item, index) in items" :key="index" class="flex items-center gap-1.5">
                <ChevronRight class="size-3.5 text-muted-foreground/60" />
                <Link
                    v-if="item.href"
                    :href="item.href"
                    class="transition-colors hover:text-foreground"
                >
                    {{ item.label }}
                </Link>
                <span v-else class="font-medium text-foreground">
                    {{ item.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Tag } from '@/types';

defineProps<{
    tag: Tag;
}>();
</script>

<template>
    <Link
        :href="`/tag/${tag.slug}`"
        class="inline-flex items-center rounded-full border border-border bg-muted/50 px-2.5 py-0.5 text-xs font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
    >
        {{ tag.name }}
    </Link>
</template>

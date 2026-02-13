<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Star } from 'lucide-vue-next';
import PlatformBadge from '@/components/public/PlatformBadge.vue';
import TagBadge from '@/components/public/TagBadge.vue';
import { Badge } from '@/components/ui/badge';
import type { Tool } from '@/types';

defineProps<{
    tool: Tool;
}>();
</script>

<template>
    <Link
        :href="`/outil/${tool.slug}`"
        class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card text-card-foreground shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
    >
        <div v-if="tool.is_sponsored" class="absolute right-3 top-3 z-10">
            <Badge class="gap-1 bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                <Star class="size-3" />
                Sponsorisé
            </Badge>
        </div>

        <div class="flex flex-col gap-4 p-5">
            <div class="flex items-start gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-border bg-muted/50">
                    <img
                        :src="tool.logo_url"
                        :alt="`Logo ${tool.name}`"
                        class="size-10 object-contain"
                        width="40"
                        height="40"
                        loading="lazy"
                    />
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="truncate text-base font-semibold text-foreground group-hover:text-primary">
                        {{ tool.name }}
                    </h3>
                    <Badge v-if="tool.category" variant="secondary" class="mt-1">
                        {{ tool.category.name }}
                    </Badge>
                </div>
            </div>

            <p v-if="tool.description" class="line-clamp-2 text-sm text-muted-foreground">
                {{ tool.description }}
            </p>

            <div v-if="tool.tags?.length" class="flex flex-wrap gap-1.5" @click.prevent>
                <TagBadge
                    v-for="tag in tool.tags.slice(0, 4)"
                    :key="tag.id"
                    :tag="tag"
                />
            </div>

            <div v-if="tool.platforms?.length" class="flex flex-wrap gap-1.5">
                <PlatformBadge
                    v-for="platform in tool.platforms"
                    :key="platform"
                    :platform="platform"
                />
            </div>
        </div>
    </Link>
</template>

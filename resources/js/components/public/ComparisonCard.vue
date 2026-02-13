<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Comparison } from '@/types';

defineProps<{
    comparison: Comparison;
}>();
</script>

<template>
    <Link
        :href="`/comparatif/${comparison.slug}`"
        class="group flex flex-col overflow-hidden rounded-xl border border-border bg-card text-card-foreground shadow-sm transition-all duration-200 hover:scale-[1.02] hover:shadow-md"
    >
        <div class="flex flex-col gap-4 p-5">
            <div class="flex items-center justify-center gap-4">
                <div class="flex flex-col items-center gap-2">
                    <div class="flex size-12 items-center justify-center overflow-hidden rounded-lg border border-border bg-muted/50">
                        <img
                            :src="comparison.tool_a.logo_url"
                            :alt="`Logo ${comparison.tool_a.name}`"
                            class="size-10 object-contain"
                            width="40"
                            height="40"
                            loading="lazy"
                        />
                    </div>
                    <span class="max-w-[100px] truncate text-sm font-medium text-foreground">
                        {{ comparison.tool_a.name }}
                    </span>
                </div>

                <span class="shrink-0 text-lg font-bold text-muted-foreground">VS</span>

                <div class="flex flex-col items-center gap-2">
                    <div class="flex size-12 items-center justify-center overflow-hidden rounded-lg border border-border bg-muted/50">
                        <img
                            :src="comparison.tool_b.logo_url"
                            :alt="`Logo ${comparison.tool_b.name}`"
                            class="size-10 object-contain"
                            width="40"
                            height="40"
                            loading="lazy"
                        />
                    </div>
                    <span class="max-w-[100px] truncate text-sm font-medium text-foreground">
                        {{ comparison.tool_b.name }}
                    </span>
                </div>
            </div>

            <p v-if="comparison.verdict" class="line-clamp-2 text-center text-sm text-muted-foreground">
                {{ comparison.verdict }}
            </p>
        </div>
    </Link>
</template>

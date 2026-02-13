<script setup lang="ts">
import { Check, Star } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import type { PricingPlan } from '@/types';

defineProps<{
    pricing: PricingPlan[];
}>();

function getPricingBadge(price: string): { label: string; classes: string } {
    const lower = price.toLowerCase();
    if (lower === 'gratuit' || lower === 'free' || lower === '0' || lower === '0€') {
        return { label: 'Gratuit', classes: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' };
    }
    if (lower.includes('freemium')) {
        return { label: 'Freemium', classes: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' };
    }
    return { label: 'Payant', classes: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300' };
}
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
            v-for="(plan, index) in pricing"
            :key="index"
            class="relative flex flex-col rounded-xl border p-5 transition-shadow"
            :class="plan.is_popular
                ? 'border-primary bg-primary/5 shadow-md'
                : 'border-border bg-card shadow-sm'"
        >
            <div v-if="plan.is_popular" class="absolute -top-3 left-1/2 -translate-x-1/2">
                <Badge class="gap-1 bg-primary text-primary-foreground">
                    <Star class="size-3" />
                    Populaire
                </Badge>
            </div>

            <div class="mb-4 flex items-start justify-between">
                <div>
                    <h4 class="text-base font-semibold text-foreground">{{ plan.name }}</h4>
                    <div class="mt-1 flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-foreground">{{ plan.price }}</span>
                        <span v-if="plan.period" class="text-sm text-muted-foreground">/ {{ plan.period }}</span>
                    </div>
                </div>
                <Badge :class="getPricingBadge(plan.price).classes" class="shrink-0">
                    {{ getPricingBadge(plan.price).label }}
                </Badge>
            </div>

            <ul v-if="plan.features?.length" class="flex flex-col gap-2">
                <li v-for="(feature, fIndex) in plan.features" :key="fIndex" class="flex items-start gap-2 text-sm text-muted-foreground">
                    <Check class="mt-0.5 size-4 shrink-0 text-primary" />
                    <span>{{ feature }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

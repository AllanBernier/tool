<script setup lang="ts">
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import type { FaqItem } from '@/types';

defineProps<{
    faq: FaqItem[];
}>();

const openIndex = ref<number | null>(null);

function toggle(index: number): void {
    openIndex.value = openIndex.value === index ? null : index;
}
</script>

<template>
    <div class="flex flex-col divide-y divide-border rounded-xl border border-border">
        <Collapsible
            v-for="(item, index) in faq"
            :key="index"
            :open="openIndex === index"
            @update:open="toggle(index)"
        >
            <CollapsibleTrigger
                class="flex w-full items-center justify-between px-5 py-4 text-left text-sm font-medium text-foreground transition-colors hover:bg-muted/50"
            >
                <span>{{ item.question }}</span>
                <ChevronDown
                    class="size-4 shrink-0 text-muted-foreground transition-transform duration-200"
                    :class="openIndex === index ? 'rotate-180' : ''"
                />
            </CollapsibleTrigger>
            <CollapsibleContent>
                <div class="px-5 pb-4 text-sm text-muted-foreground">
                    {{ item.answer }}
                </div>
            </CollapsibleContent>
        </Collapsible>
    </div>
</template>

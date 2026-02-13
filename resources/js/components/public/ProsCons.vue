<script setup lang="ts">
import { Check, X } from 'lucide-vue-next';

defineProps<{
    pros: string[];
    cons: string[];
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div
            v-if="pros.length"
            class="rounded-xl border border-green-200 bg-green-50/50 p-5 dark:border-green-900/50 dark:bg-green-950/20"
        >
            <h4
                class="mb-3 text-sm font-semibold text-green-700 dark:text-green-400"
            >
                Avantages
            </h4>
            <ul class="flex flex-col gap-2">
                <li
                    v-for="(pro, index) in pros"
                    :key="index"
                    class="flex items-start gap-2 text-sm text-foreground"
                >
                    <Check
                        class="mt-0.5 size-4 shrink-0 text-green-600 dark:text-green-400"
                    />
                    <span>{{ pro }}</span>
                </li>
            </ul>
        </div>

        <div
            v-if="cons.length"
            class="rounded-xl border border-red-200 bg-red-50/50 p-5 dark:border-red-900/50 dark:bg-red-950/20"
        >
            <h4
                class="mb-3 text-sm font-semibold text-red-700 dark:text-red-400"
            >
                Inconvénients
            </h4>
            <ul class="flex flex-col gap-2">
                <li
                    v-for="(con, index) in cons"
                    :key="index"
                    class="flex items-start gap-2 text-sm text-foreground"
                >
                    <X
                        class="mt-0.5 size-4 shrink-0 text-red-600 dark:text-red-400"
                    />
                    <span>{{ con }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

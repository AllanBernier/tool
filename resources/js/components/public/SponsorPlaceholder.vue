<script setup lang="ts">
import { Sparkles } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        variant?: 'banner' | 'card';
    }>(),
    { variant: 'banner' },
);
</script>

<template>
    <div
        v-if="variant === 'banner'"
        class="flex items-center justify-center gap-3 rounded-xl border border-dashed border-border bg-muted/20 px-6 py-4 text-center"
    >
        <Sparkles class="size-5 shrink-0 text-muted-foreground/50" />
        <p class="text-sm text-muted-foreground">
            <span class="font-medium">Espace sponsor</span> — Mettez en avant
            votre outil auprès de milliers de développeurs.
        </p>
    </div>

    <div
        v-else
        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-muted/20 p-6 text-center"
    >
        <Sparkles class="size-6 text-muted-foreground/50" />
        <p class="mt-2 text-sm font-medium text-muted-foreground">
            Votre outil ici
        </p>
        <p class="mt-1 text-xs text-muted-foreground/70">
            Espace sponsorisé disponible
        </p>
    </div>
</template>

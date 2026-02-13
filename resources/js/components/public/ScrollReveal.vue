<script setup lang="ts">
import { useScrollReveal } from '@/composables/useScrollReveal';

withDefaults(
    defineProps<{
        delay?: number;
    }>(),
    { delay: 0 },
);

const { containerRef, isVisible } = useScrollReveal();
</script>

<template>
    <div
        ref="containerRef"
        class="scroll-reveal"
        :class="{ 'is-visible': isVisible }"
        :style="delay ? `transition-delay: ${delay}ms` : undefined"
    >
        <slot />
    </div>
</template>

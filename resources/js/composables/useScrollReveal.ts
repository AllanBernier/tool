import { onMounted, onUnmounted, ref, type Ref } from 'vue';

export function useScrollReveal(threshold = 0.1): {
    containerRef: Ref<HTMLElement | null>;
    isVisible: Ref<boolean>;
} {
    const containerRef = ref<HTMLElement | null>(null);
    const isVisible = ref(false);
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (!containerRef.value) return;

        const prefersReducedMotion = window.matchMedia(
            '(prefers-reduced-motion: reduce)',
        ).matches;

        if (prefersReducedMotion) {
            isVisible.value = true;
            return;
        }

        observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    isVisible.value = true;
                    observer?.disconnect();
                }
            },
            { threshold },
        );

        observer.observe(containerRef.value);
    });

    onUnmounted(() => {
        observer?.disconnect();
    });

    return { containerRef, isVisible };
}

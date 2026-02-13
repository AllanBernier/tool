<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CommandPalette from '@/components/public/CommandPalette.vue';
import PublicFooter from '@/components/public/PublicFooter.vue';
import PublicHeader from '@/components/public/PublicHeader.vue';

type CategoryLink = {
    name: string;
    slug: string;
};

const page = usePage();
const commandPalette = ref<InstanceType<typeof CommandPalette> | null>(null);

const categories = computed<CategoryLink[]>(
    () => (page.props.footerCategories as CategoryLink[]) ?? [],
);

function openSearch(): void {
    if (commandPalette.value) {
        commandPalette.value.open = true;
    }
}
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <PublicHeader @open-search="openSearch" />
        <main class="flex-1">
            <slot />
        </main>
        <PublicFooter :categories="categories" />
        <CommandPalette ref="commandPalette" />
    </div>
</template>

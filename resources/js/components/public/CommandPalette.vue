<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { FolderTree, Loader2, Search, Tag, Wrench } from 'lucide-vue-next';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogOverlay,
    DialogTitle,
} from '@/components/ui/dialog';
import SearchController from '@/actions/App/Http/Controllers/SearchController';

type SearchResult = {
    type: 'tool' | 'category' | 'tag';
    name: string;
    slug: string;
    url: string;
    description: string | null;
};

type SearchResponse = {
    tools: SearchResult[];
    categories: SearchResult[];
    tags: SearchResult[];
};

const open = ref(false);
const query = ref('');
const loading = ref(false);
const results = ref<SearchResponse>({ tools: [], categories: [], tags: [] });
const selectedIndex = ref(0);
const inputRef = ref<HTMLInputElement | null>(null);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const allResults = computed(() => [
    ...results.value.tools,
    ...results.value.categories,
    ...results.value.tags,
]);

const hasResults = computed(() => allResults.value.length > 0);
const hasQuery = computed(() => query.value.trim().length > 0);

const iconMap = {
    tool: Wrench,
    category: FolderTree,
    tag: Tag,
};

const labelMap: Record<string, string> = {
    tool: 'Outils',
    category: 'Catégories',
    tag: 'Tags',
};

const groupedResults = computed(() => {
    const groups: { type: string; label: string; items: SearchResult[] }[] = [];

    if (results.value.tools.length > 0) {
        groups.push({ type: 'tool', label: labelMap.tool, items: results.value.tools });
    }
    if (results.value.categories.length > 0) {
        groups.push({ type: 'category', label: labelMap.category, items: results.value.categories });
    }
    if (results.value.tags.length > 0) {
        groups.push({ type: 'tag', label: labelMap.tag, items: results.value.tags });
    }

    return groups;
});

function getGlobalIndex(groupIndex: number, itemIndex: number): number {
    let index = 0;
    for (let i = 0; i < groupIndex; i++) {
        index += groupedResults.value[i].items.length;
    }
    return index + itemIndex;
}

async function fetchResults(): Promise<void> {
    const q = query.value.trim();
    if (q === '') {
        results.value = { tools: [], categories: [], tags: [] };
        return;
    }

    loading.value = true;
    try {
        const response = await fetch(SearchController.url({ query: { query: q } }), {
            headers: { Accept: 'application/json' },
        });
        results.value = await response.json();
        selectedIndex.value = 0;
    } finally {
        loading.value = false;
    }
}

function debouncedSearch(): void {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    debounceTimer = setTimeout(fetchResults, 300);
}

watch(query, debouncedSearch);

function navigateTo(result: SearchResult): void {
    open.value = false;
    router.visit(result.url);
}

function handleKeydown(event: KeyboardEvent): void {
    if (!open.value) {
        return;
    }

    const total = allResults.value.length;

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        selectedIndex.value = total > 0 ? (selectedIndex.value + 1) % total : 0;
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        selectedIndex.value = total > 0 ? (selectedIndex.value - 1 + total) % total : 0;
    } else if (event.key === 'Enter') {
        event.preventDefault();
        const result = allResults.value[selectedIndex.value];
        if (result) {
            navigateTo(result);
        }
    }
}

function handleGlobalKeydown(event: KeyboardEvent): void {
    if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
        event.preventDefault();
        open.value = true;
    }
}

function handleOpenChange(value: boolean): void {
    open.value = value;
    if (!value) {
        query.value = '';
        results.value = { tools: [], categories: [], tags: [] };
        selectedIndex.value = 0;
    }
}

watch(open, async (isOpen) => {
    if (isOpen) {
        await nextTick();
        inputRef.value?.focus();
    }
});

onMounted(() => {
    document.addEventListener('keydown', handleGlobalKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleGlobalKeydown);
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
});

defineExpose({ open });
</script>

<template>
    <Dialog :open="open" @update:open="handleOpenChange">
        <DialogContent
            :show-close-button="false"
            class="top-[20%] w-[calc(100%-2rem)] translate-y-0 gap-0 overflow-hidden p-0 sm:max-w-lg"
            @keydown="handleKeydown"
        >
            <DialogTitle class="sr-only">Rechercher</DialogTitle>
            <div class="flex items-center border-b border-border px-3">
                <Search class="mr-2 size-4 shrink-0 text-muted-foreground" />
                <input
                    ref="inputRef"
                    v-model="query"
                    type="text"
                    placeholder="Rechercher un outil, une catégorie, un tag..."
                    class="flex h-12 w-full bg-transparent text-sm text-foreground placeholder:text-muted-foreground focus:outline-none"
                />
                <Loader2
                    v-if="loading"
                    class="ml-2 size-4 shrink-0 animate-spin text-muted-foreground"
                />
            </div>
            <div class="max-h-72 overflow-y-auto">
                <!-- Empty state -->
                <div
                    v-if="!hasQuery"
                    class="px-4 py-8 text-center text-sm text-muted-foreground"
                >
                    Tapez pour rechercher
                </div>

                <!-- No results -->
                <div
                    v-else-if="hasQuery && !loading && !hasResults"
                    class="px-4 py-8 text-center text-sm text-muted-foreground"
                >
                    Aucun résultat pour « {{ query }} »
                </div>

                <!-- Results -->
                <template v-else-if="hasResults">
                    <div
                        v-for="(group, groupIndex) in groupedResults"
                        :key="group.type"
                    >
                        <div class="px-3 py-2 text-xs font-semibold text-muted-foreground">
                            {{ group.label }}
                        </div>
                        <button
                            v-for="(result, itemIndex) in group.items"
                            :key="result.slug"
                            class="flex w-full cursor-pointer items-center gap-3 px-3 py-2.5 text-left text-sm transition-colors hover:bg-accent"
                            :class="{ 'bg-accent': getGlobalIndex(groupIndex, itemIndex) === selectedIndex }"
                            @click="navigateTo(result)"
                            @mouseenter="selectedIndex = getGlobalIndex(groupIndex, itemIndex)"
                        >
                            <component
                                :is="iconMap[result.type]"
                                class="size-4 shrink-0 text-muted-foreground"
                            />
                            <div class="min-w-0 flex-1">
                                <div class="truncate font-medium">{{ result.name }}</div>
                                <div
                                    v-if="result.description"
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ result.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </template>
            </div>
            <div class="hidden items-center justify-end gap-3 border-t border-border px-3 py-2 text-xs text-muted-foreground sm:flex">
                <span class="flex items-center gap-1">
                    <kbd class="rounded border border-border bg-muted px-1 py-0.5 font-mono text-[10px]">↑↓</kbd>
                    naviguer
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="rounded border border-border bg-muted px-1 py-0.5 font-mono text-[10px]">↵</kbd>
                    ouvrir
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="rounded border border-border bg-muted px-1 py-0.5 font-mono text-[10px]">esc</kbd>
                    fermer
                </span>
            </div>
        </DialogContent>
    </Dialog>
</template>

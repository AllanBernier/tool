<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { index as comparisonsIndex, store } from '@/routes/admin/comparatifs';

type ToolOption = {
    id: string;
    name: string;
    slug: string;
};

const props = defineProps<{
    tools: ToolOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Comparatifs', href: comparisonsIndex().url },
    { title: 'Créer' },
];

const form = useForm({
    tool_a_id: '',
    tool_b_id: '',
});

const searchA = ref('');
const searchB = ref('');

const filteredToolsA = computed(() => {
    if (!searchA.value) return props.tools;
    const q = searchA.value.toLowerCase();
    return props.tools.filter((t) => t.name.toLowerCase().includes(q));
});

const filteredToolsB = computed(() => {
    if (!searchB.value) return props.tools;
    const q = searchB.value.toLowerCase();
    return props.tools.filter((t) => t.name.toLowerCase().includes(q));
});

const selectedToolA = computed(() => props.tools.find((t) => t.id === form.tool_a_id));
const selectedToolB = computed(() => props.tools.find((t) => t.id === form.tool_b_id));

const slugPreview = computed(() => {
    if (!selectedToolA.value || !selectedToolB.value) return '';
    return `${selectedToolA.value.slug}-vs-${selectedToolB.value.slug}`;
});

function submit() {
    form.post(store.url());
}
</script>

<template>
    <Head title="Créer un comparatif" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl p-4">
            <Heading title="Créer un comparatif" description="Comparer deux outils de développement" />

            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="tool_a_id">Outil A</Label>
                    <Input
                        v-model="searchA"
                        placeholder="Rechercher un outil..."
                        class="mb-2"
                    />
                    <select
                        id="tool_a_id"
                        v-model="form.tool_a_id"
                        class="border-input bg-background ring-offset-background focus-visible:ring-ring h-9 w-full rounded-md border px-3 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                        required
                    >
                        <option value="" disabled>Sélectionner l'outil A</option>
                        <option v-for="tool in filteredToolsA" :key="tool.id" :value="tool.id">
                            {{ tool.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.tool_a_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="tool_b_id">Outil B</Label>
                    <Input
                        v-model="searchB"
                        placeholder="Rechercher un outil..."
                        class="mb-2"
                    />
                    <select
                        id="tool_b_id"
                        v-model="form.tool_b_id"
                        class="border-input bg-background ring-offset-background focus-visible:ring-ring h-9 w-full rounded-md border px-3 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                        required
                    >
                        <option value="" disabled>Sélectionner l'outil B</option>
                        <option v-for="tool in filteredToolsB" :key="tool.id" :value="tool.id">
                            {{ tool.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.tool_b_id" />
                </div>

                <div v-if="slugPreview" class="rounded-lg border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">
                        Slug généré : <span class="font-mono font-medium text-foreground">{{ slugPreview }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Créer</Button>
                    <Button variant="outline" as-child>
                        <Link :href="comparisonsIndex().url">Annuler</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

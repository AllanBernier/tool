<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ChevronDown, Image } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as comparisonsIndex, update } from '@/routes/admin/comparatifs';
import { togglePublish } from '@/routes/admin/comparisons';
import { comparison as generateComparison } from '@/routes/admin/generate';
import { type BreadcrumbItem } from '@/types';

type ToolInfo = {
    id: string;
    name: string;
    slug: string;
    logo_url: string;
    logo_path: string | null;
};

type ComparisonData = {
    id: string;
    slug: string;
    content: string | null;
    verdict: string | null;
    is_published: boolean;
    generation_status: string;
    meta_title: string | null;
    meta_description: string | null;
    tool_a: ToolInfo;
    tool_b: ToolInfo;
};

const props = defineProps<{
    comparison: ComparisonData;
}>();

const comparisonLabel = `${props.comparison.tool_a.name} vs ${props.comparison.tool_b.name}`;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Comparatifs', href: comparisonsIndex().url },
    { title: comparisonLabel },
];

const seoOpen = ref(
    !!props.comparison.meta_title || !!props.comparison.meta_description,
);

const form = useForm({
    content: props.comparison.content ?? '',
    verdict: props.comparison.verdict ?? '',
    meta_title: props.comparison.meta_title ?? '',
    meta_description: props.comparison.meta_description ?? '',
});

function submit() {
    form.put(update.url(props.comparison.slug));
}

function doTogglePublish() {
    router.post(
        togglePublish.url(props.comparison.slug),
        {},
        { preserveScroll: true },
    );
}

function doGenerateComparison() {
    router.post(
        generateComparison.url(props.comparison.slug),
        {},
        { preserveScroll: true },
    );
}

const generationStatusColors: Record<string, string> = {
    pending:
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    generating: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    completed:
        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};

const generationStatusLabels: Record<string, string> = {
    pending: 'En attente',
    generating: 'Génération',
    completed: 'Terminé',
    failed: 'Échoué',
};
</script>

<template>
    <Head :title="`Modifier ${comparisonLabel}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl p-4">
            <div class="mb-6 flex items-start justify-between">
                <Heading
                    :title="`Modifier « ${comparisonLabel} »`"
                    description="Modifier le contenu du comparatif"
                />
                <div class="flex items-center gap-2">
                    <Badge
                        :class="
                            generationStatusColors[
                                comparison.generation_status
                            ] ?? ''
                        "
                        variant="outline"
                    >
                        {{
                            generationStatusLabels[
                                comparison.generation_status
                            ] ?? comparison.generation_status
                        }}
                    </Badge>
                    <Badge
                        :class="
                            comparison.is_published
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
                        "
                        variant="outline"
                    >
                        {{ comparison.is_published ? 'Publié' : 'Brouillon' }}
                    </Badge>
                    <Button
                        variant="outline"
                        size="sm"
                        @click="doTogglePublish"
                    >
                        {{ comparison.is_published ? 'Dépublier' : 'Publier' }}
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="
                            comparison.generation_status === 'generating'
                        "
                        @click="doGenerateComparison"
                    >
                        {{
                            comparison.generation_status === 'generating'
                                ? 'Génération en cours...'
                                : "Générer avec l'IA"
                        }}
                    </Button>
                </div>
            </div>

            <div class="mb-6 flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <img
                        v-if="comparison.tool_a.logo_path"
                        :src="comparison.tool_a.logo_url"
                        :alt="comparison.tool_a.name"
                        class="size-12 rounded-lg object-contain"
                    />
                    <div
                        v-else
                        class="flex size-12 items-center justify-center rounded-lg bg-muted"
                    >
                        <Image class="size-6 text-muted-foreground" />
                    </div>
                    <span class="text-lg font-medium">{{
                        comparison.tool_a.name
                    }}</span>
                </div>
                <span class="text-muted-foreground">vs</span>
                <div class="flex items-center gap-3">
                    <img
                        v-if="comparison.tool_b.logo_path"
                        :src="comparison.tool_b.logo_url"
                        :alt="comparison.tool_b.name"
                        class="size-12 rounded-lg object-contain"
                    />
                    <div
                        v-else
                        class="flex size-12 items-center justify-center rounded-lg bg-muted"
                    >
                        <Image class="size-6 text-muted-foreground" />
                    </div>
                    <span class="text-lg font-medium">{{
                        comparison.tool_b.name
                    }}</span>
                </div>
            </div>

            <form class="space-y-8" @submit.prevent="submit">
                <div
                    class="space-y-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border"
                >
                    <h3 class="text-lg font-medium">Contenu</h3>

                    <div class="grid gap-2">
                        <Label for="content">Contenu complet (Markdown)</Label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            class="flex min-h-[200px] w-full rounded-md border border-input bg-background px-3 py-2 font-mono text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Contenu complet en Markdown"
                            rows="10"
                        ></textarea>
                        <InputError :message="form.errors.content" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="verdict">Verdict</Label>
                        <textarea
                            id="verdict"
                            v-model="form.verdict"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Verdict de la comparaison"
                            rows="3"
                        ></textarea>
                        <InputError :message="form.errors.verdict" />
                    </div>
                </div>

                <Collapsible v-model:open="seoOpen">
                    <CollapsibleTrigger as-child>
                        <Button
                            variant="ghost"
                            class="w-full justify-between"
                            type="button"
                        >
                            SEO (optionnel)
                            <ChevronDown
                                class="size-4 transition-transform"
                                :class="{ 'rotate-180': seoOpen }"
                            />
                        </Button>
                    </CollapsibleTrigger>
                    <CollapsibleContent class="space-y-4 pt-4">
                        <div class="grid gap-2">
                            <Label for="meta_title">Meta Title</Label>
                            <Input
                                id="meta_title"
                                v-model="form.meta_title"
                                placeholder="Titre SEO (max 70 caractères)"
                                maxlength="70"
                            />
                            <InputError :message="form.errors.meta_title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="meta_description"
                                >Meta Description</Label
                            >
                            <textarea
                                id="meta_description"
                                v-model="form.meta_description"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Description SEO (max 160 caractères)"
                                maxlength="160"
                                rows="2"
                            ></textarea>
                            <InputError
                                :message="form.errors.meta_description"
                            />
                        </div>
                    </CollapsibleContent>
                </Collapsible>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Enregistrer</Button>
                    <Button variant="outline" as-child>
                        <Link :href="comparisonsIndex().url">Annuler</Link>
                    </Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-show="form.recentlySuccessful"
                            class="text-sm text-green-600"
                        >
                            Enregistré.
                        </p>
                    </Transition>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

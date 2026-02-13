<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronDown, Plus, X, Image } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { type BreadcrumbItem } from '@/types';
import { index as toolsIndex, update } from '@/routes/admin/outils';
import { togglePublish } from '@/routes/admin/tools';
import AlternativesPanel from './AlternativesPanel.vue';
import ComparisonsPanel from './ComparisonsPanel.vue';

type Category = {
    id: string;
    name: string;
};

type Tag = {
    id: string;
    name: string;
};

type FaqItem = {
    question: string;
    answer: string;
};

type PricingItem = {
    plan: string;
    price: number | string;
};

type Alternative = {
    id: string;
    name: string;
    slug: string;
    is_published: boolean;
    generation_status: string;
};

type ComparisonTool = {
    id: string;
    name: string;
    slug: string;
};

type Comparison = {
    id: string;
    slug: string;
    is_published: boolean;
    generation_status: string;
    tool_a: ComparisonTool | null;
    tool_b: ComparisonTool | null;
};

type ToolData = {
    id: string;
    name: string;
    slug: string;
    url: string;
    logo_url: string;
    logo_path: string | null;
    description: string | null;
    content: string | null;
    pricing: PricingItem[] | null;
    pros: string[] | null;
    cons: string[] | null;
    features: string[] | null;
    faq: FaqItem[] | null;
    platforms: string[] | null;
    category_id: string;
    is_published: boolean;
    generation_status: string;
    meta_title: string | null;
    meta_description: string | null;
    tags: Tag[];
    alternatives: Alternative[];
    comparisons_as_tool_a: Comparison[];
    comparisons_as_tool_b: Comparison[];
};

const props = defineProps<{
    tool: ToolData;
    categories: Category[];
    tags: Tag[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Outils', href: toolsIndex().url },
    { title: props.tool.name },
];

const seoOpen = ref(!!props.tool.meta_title || !!props.tool.meta_description);

const form = useForm({
    name: props.tool.name,
    url: props.tool.url,
    category_id: props.tool.category_id,
    tags: props.tool.tags.map((t) => t.id),
    description: props.tool.description ?? '',
    content: props.tool.content ?? '',
    pricing: props.tool.pricing ?? [],
    pros: props.tool.pros ?? [],
    cons: props.tool.cons ?? [],
    features: props.tool.features ?? [],
    faq: props.tool.faq ?? [],
    platforms: props.tool.platforms ?? [],
    meta_title: props.tool.meta_title ?? '',
    meta_description: props.tool.meta_description ?? '',
});

function toggleTag(tagId: string) {
    const index = form.tags.indexOf(tagId);
    if (index === -1) {
        form.tags.push(tagId);
    } else {
        form.tags.splice(index, 1);
    }
}

const allPlatforms = ['Web', 'Desktop', 'Mobile', 'API', 'CLI'];

function togglePlatform(platform: string) {
    const index = form.platforms.indexOf(platform);
    if (index === -1) {
        form.platforms.push(platform);
    } else {
        form.platforms.splice(index, 1);
    }
}

function addFeature() {
    form.features.push('');
}

function removeFeature(index: number) {
    form.features.splice(index, 1);
}

function addPro() {
    form.pros.push('');
}

function removePro(index: number) {
    form.pros.splice(index, 1);
}

function addCon() {
    form.cons.push('');
}

function removeCon(index: number) {
    form.cons.splice(index, 1);
}

function addFaq() {
    form.faq.push({ question: '', answer: '' });
}

function removeFaq(index: number) {
    form.faq.splice(index, 1);
}

function addPricing() {
    form.pricing.push({ plan: '', price: 0 });
}

function removePricing(index: number) {
    form.pricing.splice(index, 1);
}

function submit() {
    form.put(update.url(props.tool.slug));
}

function doTogglePublish() {
    router.post(togglePublish.url(props.tool.slug), {}, { preserveScroll: true });
}

const generationStatusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    generating: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
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
    <Head :title="`Modifier ${tool.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl p-4">
            <div class="mb-6 flex items-start justify-between">
                <Heading :title="`Modifier « ${tool.name} »`" description="Modifier les informations de l'outil" />
                <div class="flex items-center gap-2">
                    <Badge
                        :class="generationStatusColors[tool.generation_status] ?? ''"
                        variant="outline"
                    >
                        {{ generationStatusLabels[tool.generation_status] ?? tool.generation_status }}
                    </Badge>
                    <Badge
                        :class="tool.is_published
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                        variant="outline"
                    >
                        {{ tool.is_published ? 'Publié' : 'Brouillon' }}
                    </Badge>
                    <Button variant="outline" size="sm" @click="doTogglePublish">
                        {{ tool.is_published ? 'Dépublier' : 'Publier' }}
                    </Button>
                    <Button variant="outline" size="sm" disabled>
                        Générer avec l'IA
                    </Button>
                </div>
            </div>

            <div class="mb-6 flex items-center gap-4">
                <img
                    v-if="tool.logo_path"
                    :src="tool.logo_url"
                    :alt="tool.name"
                    class="size-16 rounded-lg object-contain"
                />
                <div v-else class="flex size-16 items-center justify-center rounded-lg bg-muted">
                    <Image class="size-8 text-muted-foreground" />
                </div>
            </div>

            <form class="space-y-8" @submit.prevent="submit">
                <div class="space-y-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-medium">Informations de base</h3>

                    <div class="grid gap-2">
                        <Label for="name">Nom</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="Nom de l'outil"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="url">URL</Label>
                        <Input
                            id="url"
                            v-model="form.url"
                            type="url"
                            placeholder="https://example.com"
                            required
                        />
                        <InputError :message="form.errors.url" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="category_id">Catégorie</Label>
                        <select
                            id="category_id"
                            v-model="form.category_id"
                            class="border-input bg-background ring-offset-background focus-visible:ring-ring h-9 w-full rounded-md border px-3 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                            required
                        >
                            <option value="" disabled>Sélectionner une catégorie</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.category_id" />
                    </div>

                    <div v-if="tags.length > 0" class="grid gap-2">
                        <Label>Tags</Label>
                        <div class="flex flex-wrap gap-3">
                            <label
                                v-for="tag in tags"
                                :key="tag.id"
                                class="flex items-center gap-2 text-sm"
                            >
                                <Checkbox
                                    :checked="form.tags.includes(tag.id)"
                                    @update:checked="toggleTag(tag.id)"
                                />
                                {{ tag.name }}
                            </label>
                        </div>
                        <InputError :message="form.errors.tags" />
                    </div>
                </div>

                <div class="space-y-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-medium">Contenu</h3>

                    <div class="grid gap-2">
                        <Label for="description">Description courte</Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Description courte de l'outil"
                            rows="3"
                        ></textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="content">Contenu complet (Markdown)</Label>
                        <textarea
                            id="content"
                            v-model="form.content"
                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[200px] w-full rounded-md border px-3 py-2 font-mono text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Contenu complet en Markdown"
                            rows="10"
                        ></textarea>
                        <InputError :message="form.errors.content" />
                    </div>
                </div>

                <div class="space-y-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Fonctionnalités</h3>
                        <Button type="button" variant="outline" size="sm" @click="addFeature">
                            <Plus class="mr-1 size-4" />
                            Ajouter
                        </Button>
                    </div>
                    <div v-for="(_, i) in form.features" :key="i" class="flex items-center gap-2">
                        <Input
                            v-model="form.features[i]"
                            placeholder="Fonctionnalité"
                        />
                        <Button type="button" variant="ghost" size="icon" @click="removeFeature(i)">
                            <X class="size-4 text-red-500" />
                        </Button>
                    </div>
                    <p v-if="form.features.length === 0" class="text-sm text-muted-foreground">Aucune fonctionnalité ajoutée.</p>
                    <InputError :message="form.errors.features" />
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">Avantages</h3>
                            <Button type="button" variant="outline" size="sm" @click="addPro">
                                <Plus class="mr-1 size-4" />
                                Ajouter
                            </Button>
                        </div>
                        <div v-for="(_, i) in form.pros" :key="i" class="flex items-center gap-2">
                            <Input v-model="form.pros[i]" placeholder="Avantage" />
                            <Button type="button" variant="ghost" size="icon" @click="removePro(i)">
                                <X class="size-4 text-red-500" />
                            </Button>
                        </div>
                        <p v-if="form.pros.length === 0" class="text-sm text-muted-foreground">Aucun avantage.</p>
                        <InputError :message="form.errors.pros" />
                    </div>

                    <div class="space-y-4 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">Inconvénients</h3>
                            <Button type="button" variant="outline" size="sm" @click="addCon">
                                <Plus class="mr-1 size-4" />
                                Ajouter
                            </Button>
                        </div>
                        <div v-for="(_, i) in form.cons" :key="i" class="flex items-center gap-2">
                            <Input v-model="form.cons[i]" placeholder="Inconvénient" />
                            <Button type="button" variant="ghost" size="icon" @click="removeCon(i)">
                                <X class="size-4 text-red-500" />
                            </Button>
                        </div>
                        <p v-if="form.cons.length === 0" class="text-sm text-muted-foreground">Aucun inconvénient.</p>
                        <InputError :message="form.errors.cons" />
                    </div>
                </div>

                <div class="space-y-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Tarification</h3>
                        <Button type="button" variant="outline" size="sm" @click="addPricing">
                            <Plus class="mr-1 size-4" />
                            Ajouter un plan
                        </Button>
                    </div>
                    <div v-for="(item, i) in form.pricing" :key="i" class="flex items-center gap-2">
                        <Input v-model="item.plan" placeholder="Nom du plan" class="flex-1" />
                        <Input v-model="item.price" type="number" placeholder="Prix" class="w-32" />
                        <Button type="button" variant="ghost" size="icon" @click="removePricing(i)">
                            <X class="size-4 text-red-500" />
                        </Button>
                    </div>
                    <p v-if="form.pricing.length === 0" class="text-sm text-muted-foreground">Aucun plan tarifaire.</p>
                    <InputError :message="form.errors.pricing" />
                </div>

                <div class="space-y-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 class="text-lg font-medium">Plateformes</h3>
                    <div class="flex flex-wrap gap-4">
                        <label
                            v-for="platform in allPlatforms"
                            :key="platform"
                            class="flex items-center gap-2 text-sm"
                        >
                            <Checkbox
                                :checked="form.platforms.includes(platform)"
                                @update:checked="togglePlatform(platform)"
                            />
                            {{ platform }}
                        </label>
                    </div>
                    <InputError :message="form.errors.platforms" />
                </div>

                <div class="space-y-6 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">FAQ</h3>
                        <Button type="button" variant="outline" size="sm" @click="addFaq">
                            <Plus class="mr-1 size-4" />
                            Ajouter
                        </Button>
                    </div>
                    <div v-for="(item, i) in form.faq" :key="i" class="space-y-2 rounded-lg border border-sidebar-border/30 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 space-y-2">
                                <Input v-model="item.question" placeholder="Question" />
                                <textarea
                                    v-model="item.answer"
                                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[60px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Réponse"
                                    rows="2"
                                ></textarea>
                            </div>
                            <Button type="button" variant="ghost" size="icon" class="ml-2" @click="removeFaq(i)">
                                <X class="size-4 text-red-500" />
                            </Button>
                        </div>
                    </div>
                    <p v-if="form.faq.length === 0" class="text-sm text-muted-foreground">Aucune question/réponse.</p>
                    <InputError :message="form.errors.faq" />
                </div>

                <Collapsible v-model:open="seoOpen">
                    <CollapsibleTrigger as-child>
                        <Button variant="ghost" class="w-full justify-between" type="button">
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
                                placeholder="Titre SEO"
                            />
                            <InputError :message="form.errors.meta_title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="meta_description">Meta Description</Label>
                            <textarea
                                id="meta_description"
                                v-model="form.meta_description"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Description SEO"
                                rows="2"
                            ></textarea>
                            <InputError :message="form.errors.meta_description" />
                        </div>
                    </CollapsibleContent>
                </Collapsible>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Enregistrer</Button>
                    <Button variant="outline" as-child>
                        <Link :href="toolsIndex().url">Annuler</Link>
                    </Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="form.recentlySuccessful" class="text-sm text-green-600">
                            Enregistré.
                        </p>
                    </Transition>
                </div>
            </form>

            <div class="mt-8 space-y-6">
                <AlternativesPanel :alternatives="tool.alternatives" :tool-slug="tool.slug" />
                <ComparisonsPanel
                    :comparisons-as-tool-a="tool.comparisons_as_tool_a"
                    :comparisons-as-tool-b="tool.comparisons_as_tool_b"
                    :tool-name="tool.name"
                />
            </div>
        </div>
    </AppLayout>
</template>

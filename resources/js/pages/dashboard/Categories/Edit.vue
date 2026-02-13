<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ChevronDown } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { type BreadcrumbItem } from '@/types';
import { index as categoriesIndex, update } from '@/routes/admin/categories';

type Category = {
    id: string;
    name: string;
    slug: string;
    icon: string;
    description: string;
    meta_title: string | null;
    meta_description: string | null;
};

const props = defineProps<{
    category: Category;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Catégories', href: categoriesIndex().url },
    { title: props.category.name },
];

const seoOpen = ref(!!props.category.meta_title || !!props.category.meta_description);

const form = useForm({
    name: props.category.name,
    description: props.category.description,
    icon: props.category.icon,
    meta_title: props.category.meta_title ?? '',
    meta_description: props.category.meta_description ?? '',
});

function submit() {
    form.put(update.url(props.category.slug));
}
</script>

<template>
    <Head :title="`Modifier ${category.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl p-4">
            <Heading :title="`Modifier « ${category.name} »`" description="Modifier les informations de la catégorie" />

            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="name">Nom</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Nom de la catégorie"
                        required
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Description de la catégorie"
                        rows="3"
                        required
                    ></textarea>
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="icon">Icône (Lucide)</Label>
                    <Input
                        id="icon"
                        v-model="form.icon"
                        placeholder="ex: code, server, database"
                        required
                    />
                    <InputError :message="form.errors.icon" />
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
                        <Link :href="categoriesIndex().url">Annuler</Link>
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
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as categoriesIndex, store } from '@/routes/admin/categories';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Catégories', href: categoriesIndex().url },
    { title: 'Créer' },
];

const seoOpen = ref(false);

const form = useForm({
    name: '',
    description: '',
    icon: '',
    meta_title: '',
    meta_description: '',
});

function submit() {
    form.post(store.url());
}
</script>

<template>
    <Head title="Créer une catégorie" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl p-4">
            <Heading
                title="Créer une catégorie"
                description="Ajouter une nouvelle catégorie d'outils"
            />

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
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
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
                                placeholder="Titre SEO"
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
                                placeholder="Description SEO"
                                rows="2"
                            ></textarea>
                            <InputError
                                :message="form.errors.meta_description"
                            />
                        </div>
                    </CollapsibleContent>
                </Collapsible>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Créer</Button>
                    <Button variant="outline" as-child>
                        <Link :href="categoriesIndex().url">Annuler</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

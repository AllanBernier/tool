<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { type BreadcrumbItem } from '@/types';
import { index as toolsIndex, store } from '@/routes/admin/outils';

type Category = {
    id: string;
    name: string;
};

type Tag = {
    id: string;
    name: string;
};

const props = defineProps<{
    categories: Category[];
    tags: Tag[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Outils', href: toolsIndex().url },
    { title: 'Créer' },
];

const form = useForm({
    name: '',
    url: '',
    category_id: '',
    tags: [] as string[],
});

function toggleTag(tagId: string) {
    const index = form.tags.indexOf(tagId);
    if (index === -1) {
        form.tags.push(tagId);
    } else {
        form.tags.splice(index, 1);
    }
}

function submit() {
    form.post(store.url());
}
</script>

<template>
    <Head title="Créer un outil" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl p-4">
            <Heading title="Créer un outil" description="Ajouter un nouvel outil de développement" />

            <form class="space-y-6" @submit.prevent="submit">
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

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Créer</Button>
                    <Button variant="outline" as-child>
                        <Link :href="toolsIndex().url">Annuler</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

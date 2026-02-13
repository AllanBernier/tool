<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { index as tagsIndex, store } from '@/routes/admin/tags';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tags', href: tagsIndex().url },
    { title: 'Créer' },
];

const form = useForm({
    name: '',
});

function submit() {
    form.post(store.url());
}
</script>

<template>
    <Head title="Créer un tag" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl p-4">
            <Heading title="Créer un tag" description="Ajouter un nouveau tag" />

            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="name">Nom</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Nom du tag"
                        required
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Créer</Button>
                    <Button variant="outline" as-child>
                        <Link :href="tagsIndex().url">Annuler</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

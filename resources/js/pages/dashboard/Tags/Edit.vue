<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { index as tagsIndex, update } from '@/routes/admin/tags';

type Tag = {
    id: string;
    name: string;
    slug: string;
};

const props = defineProps<{
    tag: Tag;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tags', href: tagsIndex().url },
    { title: props.tag.name },
];

const form = useForm({
    name: props.tag.name,
});

function submit() {
    form.put(update.url(props.tag.slug));
}
</script>

<template>
    <Head :title="`Modifier ${tag.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl p-4">
            <Heading :title="`Modifier « ${tag.name} »`" description="Modifier les informations du tag" />

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
                    <Button :disabled="form.processing">Enregistrer</Button>
                    <Button variant="outline" as-child>
                        <Link :href="tagsIndex().url">Annuler</Link>
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

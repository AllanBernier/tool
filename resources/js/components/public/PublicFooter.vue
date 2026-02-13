<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

type CategoryLink = {
    name: string;
    slug: string;
};

type Props = {
    categories?: CategoryLink[];
};

withDefaults(defineProps<Props>(), {
    categories: () => [],
});

const sectionLinks = [
    { title: 'Accueil', href: '/' },
    { title: 'Outils', href: '/outils' },
    { title: 'Catégories', href: '/categories' },
    { title: 'Comparatifs', href: '/comparatifs' },
];

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="border-t border-border bg-muted/30">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Brand -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <Link
                        :href="home()"
                        class="inline-flex items-center gap-2.5"
                    >
                        <div
                            class="flex size-8 items-center justify-center rounded-lg bg-foreground"
                        >
                            <AppLogoIcon
                                class="size-5 fill-current text-background"
                            />
                        </div>
                        <span class="text-lg font-bold tracking-tight"
                            >Tool</span
                        >
                    </Link>
                    <p class="mt-3 text-sm text-muted-foreground">
                        Découvrez, comparez et choisissez les meilleurs outils
                        de développement.
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <h3
                        class="text-sm font-semibold tracking-wider text-foreground uppercase"
                    >
                        Navigation
                    </h3>
                    <ul class="mt-4 flex flex-col gap-2.5">
                        <li v-for="link in sectionLinks" :key="link.title">
                            <Link
                                :href="link.href"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                            >
                                {{ link.title }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Categories -->
                <div v-if="categories.length > 0" class="lg:col-span-2">
                    <h3
                        class="text-sm font-semibold tracking-wider text-foreground uppercase"
                    >
                        Catégories
                    </h3>
                    <ul class="mt-4 grid grid-cols-2 gap-x-6 gap-y-2.5">
                        <li v-for="category in categories" :key="category.slug">
                            <Link
                                :href="`/categories/${category.slug}`"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                            >
                                {{ category.name }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div
                class="mt-10 flex flex-col items-center gap-4 border-t border-border pt-6 sm:flex-row sm:justify-between"
            >
                <p class="text-xs text-muted-foreground">
                    &copy; {{ currentYear }} Tool. Tous droits réservés.
                </p>
                <div class="flex gap-4">
                    <Link
                        href="/mentions-legales"
                        class="text-xs text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Mentions légales
                    </Link>
                    <Link
                        href="/politique-confidentialite"
                        class="text-xs text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Politique de confidentialité
                    </Link>
                </div>
            </div>
        </div>
    </footer>
</template>

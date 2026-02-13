<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    FolderTree,
    GitCompareArrows,
    Menu,
    Moon,
    Search,
    Sun,
    Wrench,
} from 'lucide-vue-next';
import { ref } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import MobileMenu from '@/components/public/MobileMenu.vue';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/composables/useAppearance';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { home } from '@/routes';
import type { NavItem } from '@/types';

const emit = defineEmits<{
    openSearch: [];
}>();

const { appearance, updateAppearance } = useAppearance();
const { isCurrentUrl, whenCurrentUrl } = useCurrentUrl();

const mobileMenuOpen = ref(false);

const navItems: NavItem[] = [
    {
        title: 'Outils',
        href: '/outils',
        icon: Wrench,
    },
    {
        title: 'Catégories',
        href: '/categories',
        icon: FolderTree,
    },
    {
        title: 'Comparatifs',
        href: '/comparatifs',
        icon: GitCompareArrows,
    },
];

function toggleAppearance(): void {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b border-border/60 bg-background/80 backdrop-blur-lg"
    >
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
            <!-- Mobile hamburger -->
            <Button
                variant="ghost"
                size="icon"
                class="lg:hidden"
                @click="mobileMenuOpen = true"
            >
                <Menu class="size-5" />
                <span class="sr-only">Ouvrir le menu</span>
            </Button>

            <!-- Logo -->
            <Link
                :href="home()"
                class="flex items-center gap-2.5"
            >
                <div
                    class="flex size-8 items-center justify-center rounded-lg bg-foreground"
                >
                    <AppLogoIcon
                        class="size-5 fill-current text-background"
                    />
                </div>
                <span class="text-lg font-bold tracking-tight">Tool</span>
            </Link>

            <!-- Desktop nav -->
            <nav class="hidden lg:flex lg:items-center lg:gap-1">
                <Link
                    v-for="item in navItems"
                    :key="item.title"
                    :href="item.href"
                    class="relative flex items-center gap-1.5 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    :class="
                        whenCurrentUrl(
                            item.href,
                            'text-foreground',
                        )
                    "
                >
                    <component :is="item.icon" class="size-4" />
                    {{ item.title }}
                    <span
                        v-if="isCurrentUrl(item.href)"
                        class="absolute -bottom-[calc(0.5rem+1px)] left-0 h-0.5 w-full bg-foreground"
                    />
                </Link>
            </nav>

            <div class="ml-auto flex items-center gap-2">
                <!-- Search button -->
                <Button
                    variant="outline"
                    size="sm"
                    class="hidden gap-2 text-muted-foreground sm:flex"
                    @click="emit('openSearch')"
                >
                    <Search class="size-4" />
                    <span class="text-sm">Rechercher...</span>
                    <kbd
                        class="pointer-events-none ml-2 hidden rounded border border-border bg-muted px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground lg:inline-block"
                    >
                        <span class="text-xs">&#8984;</span>K
                    </kbd>
                </Button>
                <Button
                    variant="ghost"
                    size="icon"
                    class="sm:hidden"
                    @click="emit('openSearch')"
                >
                    <Search class="size-5" />
                    <span class="sr-only">Rechercher</span>
                </Button>

                <!-- Theme toggle -->
                <Button
                    variant="ghost"
                    size="icon"
                    @click="toggleAppearance"
                >
                    <Sun
                        class="size-5 rotate-0 scale-100 transition-transform dark:-rotate-90 dark:scale-0"
                    />
                    <Moon
                        class="absolute size-5 rotate-90 scale-0 transition-transform dark:rotate-0 dark:scale-100"
                    />
                    <span class="sr-only">Changer le thème</span>
                </Button>
            </div>
        </div>
    </header>

    <!-- Mobile menu -->
    <MobileMenu
        v-model:open="mobileMenuOpen"
        :nav-items="navItems"
    />
</template>

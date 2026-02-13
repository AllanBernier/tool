<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import { watch } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

type Props = {
    open: boolean;
    navItems: NavItem[];
};

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const { whenCurrentUrl } = useCurrentUrl();

const activeItemStyles =
    'bg-accent text-accent-foreground font-semibold';

watch(
    () => props.open,
    (value) => {
        if (!value) return;
    },
);
</script>

<template>
    <Sheet :open="open" @update:open="emit('update:open', $event)">
        <SheetContent side="left" class="w-[300px] p-6">
            <SheetTitle class="sr-only">Menu de navigation</SheetTitle>
            <SheetHeader class="flex justify-start text-left">
                <Link href="/" @click="emit('update:open', false)">
                    <AppLogoIcon
                        class="size-6 fill-current text-black dark:text-white"
                    />
                </Link>
            </SheetHeader>
            <nav class="flex flex-1 flex-col gap-1 py-6">
                <SheetClose :as-child="true" v-for="item in navItems" :key="item.title">
                    <Link
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-accent"
                        :class="whenCurrentUrl(item.href, activeItemStyles)"
                    >
                        <component
                            v-if="item.icon"
                            :is="item.icon"
                            class="size-5"
                        />
                        {{ item.title }}
                    </Link>
                </SheetClose>
            </nav>
        </SheetContent>
    </Sheet>
</template>

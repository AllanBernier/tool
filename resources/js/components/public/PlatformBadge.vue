<script setup lang="ts">
import { Globe, Monitor, Smartphone, Server, Terminal } from 'lucide-vue-next';
import { computed, type Component } from 'vue';

const props = defineProps<{
    platform: string;
}>();

type PlatformConfig = {
    icon: Component;
    label: string;
    classes: string;
};

const platformMap: Record<string, PlatformConfig> = {
    web: {
        icon: Globe,
        label: 'Web',
        classes:
            'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    },
    desktop: {
        icon: Monitor,
        label: 'Desktop',
        classes:
            'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
    },
    mobile: {
        icon: Smartphone,
        label: 'Mobile',
        classes:
            'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
    },
    api: {
        icon: Server,
        label: 'API',
        classes:
            'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
    },
    cli: {
        icon: Terminal,
        label: 'CLI',
        classes:
            'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    },
};

const config = computed<PlatformConfig>(() => {
    const key = props.platform.toLowerCase();
    return (
        platformMap[key] ?? {
            icon: Globe,
            label: props.platform,
            classes:
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        }
    );
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium"
        :class="config.classes"
    >
        <component :is="config.icon" class="size-3" />
        {{ config.label }}
    </span>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    schema: Record<string, unknown> | Record<string, unknown>[];
}>();

const jsonLd = computed(() => JSON.stringify(props.schema));
</script>

<template>
    <Head>
        <script head-key="json-ld" type="application/ld+json" v-html="jsonLd" />
    </Head>
</template>

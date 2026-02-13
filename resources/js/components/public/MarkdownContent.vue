<script setup lang="ts">
import DOMPurify from 'dompurify';
import MarkdownIt from 'markdown-it';
import { computed } from 'vue';

const props = defineProps<{
    content: string;
}>();

const md = new MarkdownIt({
    html: false,
    linkify: true,
    typographer: true,
});

const renderedHtml = computed(() => {
    const raw = md.render(props.content);
    return DOMPurify.sanitize(raw);
});
</script>

<template>
    <div class="prose prose-sm max-w-none dark:prose-invert prose-headings:text-foreground prose-p:text-muted-foreground prose-a:text-primary prose-a:underline prose-strong:text-foreground prose-code:rounded prose-code:bg-muted prose-code:px-1.5 prose-code:py-0.5 prose-code:text-sm prose-pre:bg-muted prose-pre:text-foreground prose-li:text-muted-foreground" v-html="renderedHtml" />
</template>

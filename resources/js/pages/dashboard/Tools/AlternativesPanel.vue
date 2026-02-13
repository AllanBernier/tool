<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    alternatives as suggestAlternatives,
    tool as generateTool,
} from '@/routes/admin/generate';

type Alternative = {
    id: string;
    name: string;
    slug: string;
    is_published: boolean;
    generation_status: string;
};

const props = defineProps<{
    alternatives: Alternative[];
    toolSlug: string;
}>();

const statusColors: Record<string, string> = {
    pending:
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    generating: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    completed:
        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};

const statusLabels: Record<string, string> = {
    pending: 'En attente',
    generating: 'Génération',
    completed: 'Terminé',
    failed: 'Échoué',
};

function doSuggestAlternatives() {
    router.post(
        suggestAlternatives.url(props.toolSlug),
        {},
        { preserveScroll: true },
    );
}

function doGenerateAlternative(altSlug: string) {
    router.post(generateTool.url(altSlug), {}, { preserveScroll: true });
}
</script>

<template>
    <div
        class="space-y-4 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border"
    >
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium">Alternatives</h3>
            <Button variant="outline" size="sm" @click="doSuggestAlternatives">
                Suggérer des alternatives
            </Button>
        </div>

        <div v-if="alternatives.length > 0" class="space-y-2">
            <div
                v-for="alt in alternatives"
                :key="alt.id"
                class="flex items-center justify-between rounded-lg border border-sidebar-border/30 px-4 py-3"
            >
                <span class="font-medium">{{ alt.name }}</span>
                <div class="flex items-center gap-2">
                    <Badge
                        :class="
                            alt.is_published
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
                        "
                        variant="outline"
                    >
                        {{ alt.is_published ? 'Publié' : 'Brouillon' }}
                    </Badge>
                    <Badge
                        :class="statusColors[alt.generation_status] ?? ''"
                        variant="outline"
                    >
                        {{
                            statusLabels[alt.generation_status] ??
                            alt.generation_status
                        }}
                    </Badge>
                    <Button
                        v-if="
                            alt.generation_status === 'pending' ||
                            alt.generation_status === 'failed'
                        "
                        variant="outline"
                        size="sm"
                        @click="doGenerateAlternative(alt.slug)"
                    >
                        Générer avec l'IA
                    </Button>
                    <span
                        v-else-if="alt.generation_status === 'generating'"
                        class="text-sm text-muted-foreground"
                    >
                        Génération en cours...
                    </span>
                </div>
            </div>
        </div>

        <p v-else class="text-sm text-muted-foreground">
            Aucune alternative pour cet outil.
        </p>
    </div>
</template>

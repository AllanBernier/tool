<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type ComparisonTool = {
    id: string;
    name: string;
    slug: string;
};

type Comparison = {
    id: string;
    slug: string;
    is_published: boolean;
    generation_status: string;
    tool_a: ComparisonTool | null;
    tool_b: ComparisonTool | null;
};

const props = defineProps<{
    comparisonsAsToolA: Comparison[];
    comparisonsAsToolB: Comparison[];
    toolName: string;
}>();

const allComparisons = [...props.comparisonsAsToolA, ...props.comparisonsAsToolB];

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    generating: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};

const statusLabels: Record<string, string> = {
    pending: 'En attente',
    generating: 'Génération',
    completed: 'Terminé',
    failed: 'Échoué',
};

function getOtherTool(comparison: Comparison): string {
    if (comparison.tool_a && comparison.tool_a.name !== props.toolName) {
        return comparison.tool_a.name;
    }
    if (comparison.tool_b && comparison.tool_b.name !== props.toolName) {
        return comparison.tool_b.name;
    }
    return '—';
}
</script>

<template>
    <div class="space-y-4 rounded-lg border border-sidebar-border/70 p-6 dark:border-sidebar-border">
        <h3 class="text-lg font-medium">Comparatifs</h3>

        <div v-if="allComparisons.length > 0" class="space-y-2">
            <div
                v-for="comparison in allComparisons"
                :key="comparison.id"
                class="flex items-center justify-between rounded-lg border border-sidebar-border/30 px-4 py-3"
            >
                <span class="font-medium">{{ toolName }} vs {{ getOtherTool(comparison) }}</span>
                <div class="flex items-center gap-2">
                    <Badge
                        :class="comparison.is_published
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                        variant="outline"
                    >
                        {{ comparison.is_published ? 'Publié' : 'Brouillon' }}
                    </Badge>
                    <Badge
                        :class="statusColors[comparison.generation_status] ?? ''"
                        variant="outline"
                    >
                        {{ statusLabels[comparison.generation_status] ?? comparison.generation_status }}
                    </Badge>
                    <Button v-if="comparison.generation_status === 'pending'" variant="outline" size="sm" disabled>
                        Générer avec l'IA
                    </Button>
                </div>
            </div>
        </div>

        <p v-else class="text-sm text-muted-foreground">
            Aucun comparatif pour cet outil.
        </p>
    </div>
</template>

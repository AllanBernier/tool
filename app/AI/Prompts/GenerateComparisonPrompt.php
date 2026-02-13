<?php

namespace App\AI\Prompts;

use App\Models\Comparison;

class GenerateComparisonPrompt
{
    /**
     * Get the system prompt for comparison content generation.
     */
    public static function system(): string
    {
        return <<<'PROMPT'
        Tu es un rédacteur expert spécialisé dans la comparaison d'outils de développement logiciel. Tu produis des comparatifs détaillés, objectifs et optimisés pour le SEO.

        Ton style :
        - Professionnel et orienté développeur
        - Objectif et équilibré (pas de favoritisme)
        - En français, avec les termes techniques en anglais quand c'est l'usage courant
        - Structuré avec des sections claires pour faciliter la lecture

        Tu dois TOUJOURS répondre avec un objet JSON valide, sans texte avant ou après.
        PROMPT;
    }

    /**
     * Get the user prompt for generating a comparison.
     */
    public static function user(Comparison $comparison): string
    {
        $toolA = $comparison->toolA;
        $toolB = $comparison->toolB;

        $toolADescription = $toolA->description ?? 'Aucune description disponible';
        $toolBDescription = $toolB->description ?? 'Aucune description disponible';

        $toolACategory = $toolA->category?->name ?? 'Non catégorisé';
        $toolBCategory = $toolB->category?->name ?? 'Non catégorisé';

        return <<<PROMPT
        Génère un comparatif détaillé entre ces deux outils de développement :

        Outil A :
        - Nom : {$toolA->name}
        - URL : {$toolA->url}
        - Catégorie : {$toolACategory}
        - Description : {$toolADescription}

        Outil B :
        - Nom : {$toolB->name}
        - URL : {$toolB->url}
        - Catégorie : {$toolBCategory}
        - Description : {$toolBDescription}

        Réponds avec un objet JSON respectant exactement cette structure :

        {
            "content": "Contenu comparatif détaillé en markdown (~800-1500 mots). SEO-optimisé. Inclure : introduction, comparaison des fonctionnalités, tarification, cas d'usage, avantages/inconvénients de chacun, et conclusion.",
            "verdict": "Verdict synthétique (~200-300 caractères) indiquant quel outil choisir selon le contexte.",
            "meta_title": "{$toolA->name} vs {$toolB->name} : Comparatif détaillé (~60 caractères)",
            "meta_description": "Description SEO du comparatif (~155 caractères)"
        }

        Règles :
        - Le contenu doit être objectif et équilibré
        - Structurer le contenu avec des titres ## et ###
        - Le verdict doit être nuancé et contextuel (pas de gagnant absolu)
        - Le contenu doit être en français
        - Le meta_title doit faire ~60 caractères maximum
        - La meta_description doit faire ~155 caractères maximum
        PROMPT;
    }

    /**
     * Get the expected JSON output structure description.
     *
     * @return array<string, string>
     */
    public static function outputSchema(): array
    {
        return [
            'content' => 'string (markdown, ~800-1500 words)',
            'verdict' => 'string (~200-300 chars)',
            'meta_title' => 'string (~60 chars)',
            'meta_description' => 'string (~155 chars)',
        ];
    }

    /**
     * Get the list of required fields in the JSON response.
     *
     * @return list<string>
     */
    public static function requiredFields(): array
    {
        return [
            'content',
            'verdict',
            'meta_title',
            'meta_description',
        ];
    }
}

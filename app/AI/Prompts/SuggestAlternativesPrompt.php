<?php

namespace App\AI\Prompts;

use App\Models\Tool;

class SuggestAlternativesPrompt
{
    /**
     * Get the system prompt for alternative suggestions.
     */
    public static function system(): string
    {
        return <<<'PROMPT'
        Tu es un expert en outils de développement logiciel. Tu connais l'écosystème complet des outils disponibles pour les développeurs et tu es capable de suggérer des alternatives pertinentes.

        Ton style :
        - Professionnel et orienté développeur
        - Objectif et factuel
        - Tu ne suggères que des outils réels et existants

        Tu dois TOUJOURS répondre avec un objet JSON valide, sans texte avant ou après.
        PROMPT;
    }

    /**
     * Get the user prompt for suggesting alternatives.
     */
    public static function user(Tool $tool): string
    {
        $categoryName = $tool->category?->name ?? 'Non catégorisé';
        $description = $tool->description ?? 'Aucune description disponible';

        return <<<PROMPT
        Suggère des alternatives pour l'outil de développement suivant :

        Nom : {$tool->name}
        URL : {$tool->url}
        Catégorie : {$categoryName}
        Description : {$description}

        Réponds avec un objet JSON respectant exactement cette structure :

        {
            "alternatives": [
                {
                    "name": "Nom de l'outil alternatif",
                    "url": "https://url-officielle.com",
                    "reason": "Raison courte pour laquelle c'est une bonne alternative (~100 caractères)"
                }
            ]
        }

        Règles :
        - Suggère entre 5 et 10 alternatives
        - Ne suggère que des outils réels et existants
        - Les alternatives doivent être dans la même catégorie ou résoudre le même problème
        - Inclus un mix d'outils populaires et de moins connus
        - L'URL doit être l'URL officielle du site de l'outil
        - Ne suggère PAS l'outil lui-même comme alternative
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
            'alternatives' => 'array of {name: string, url: string, reason: string} (5-10)',
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
            'alternatives',
        ];
    }
}

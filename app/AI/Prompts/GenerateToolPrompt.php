<?php

namespace App\AI\Prompts;

use App\Models\Tool;

class GenerateToolPrompt
{
    /**
     * Get the system prompt for tool content generation.
     */
    public static function system(): string
    {
        return <<<'PROMPT'
        Tu es un rédacteur expert spécialisé dans les outils de développement logiciel. Tu rédiges des fiches produit complètes, objectives et optimisées pour le SEO.

        Ton style :
        - Professionnel et orienté développeur
        - Objectif et factuel (pas de langage marketing excessif)
        - En français, avec les termes techniques en anglais quand c'est l'usage courant
        - Structuré et facile à scanner visuellement

        Tu dois TOUJOURS répondre avec un objet JSON valide, sans texte avant ou après.
        PROMPT;
    }

    /**
     * Get the user prompt for generating a tool profile.
     */
    public static function user(Tool $tool): string
    {
        $categoryName = $tool->category?->name ?? 'Non catégorisé';

        return <<<PROMPT
        Génère une fiche complète pour l'outil de développement suivant :

        Nom : {$tool->name}
        URL : {$tool->url}
        Catégorie : {$categoryName}

        Réponds avec un objet JSON respectant exactement cette structure :

        {
            "description": "Description courte de l'outil (~150 caractères)",
            "content": "Contenu détaillé en markdown (~500-1000 mots). SEO-optimisé, structuré avec des titres ## et ###. Explique ce que fait l'outil, ses cas d'usage, son fonctionnement, et pourquoi un développeur devrait l'utiliser.",
            "pros": ["Avantage 1", "Avantage 2", "..."],
            "cons": ["Inconvénient 1", "Inconvénient 2", "..."],
            "features": ["Fonctionnalité 1", "Fonctionnalité 2", "..."],
            "faq": [
                {"question": "Question 1 ?", "answer": "Réponse 1"},
                {"question": "Question 2 ?", "answer": "Réponse 2"}
            ],
            "pricing": [
                {"plan": "Nom du plan", "price": "Prix (ex: Gratuit, 10$/mois)", "features": ["Feature 1", "Feature 2"]}
            ],
            "platforms": ["Web", "macOS", "Windows", "Linux", "..."],
            "suggested_tags": ["tag1", "tag2", "..."],
            "suggested_alternatives": ["Nom Outil Alternatif 1", "Nom Outil Alternatif 2", "..."],
            "meta_title": "Titre SEO (~60 caractères)",
            "meta_description": "Description SEO (~155 caractères)"
        }

        Règles :
        - pros : 5 à 8 éléments
        - cons : 3 à 5 éléments
        - features : 5 à 10 éléments
        - faq : 5 à 8 questions/réponses
        - suggested_tags : 3 à 8 tags pertinents (en minuscules, sans accents)
        - suggested_alternatives : 5 à 10 noms d'outils alternatifs existants
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
            'description' => 'string (~150 chars)',
            'content' => 'string (markdown, ~500-1000 words)',
            'pros' => 'array of strings (5-8)',
            'cons' => 'array of strings (3-5)',
            'features' => 'array of strings (5-10)',
            'faq' => 'array of {question: string, answer: string} (5-8)',
            'pricing' => 'array of {plan: string, price: string, features: array}',
            'platforms' => 'array of strings',
            'suggested_tags' => 'array of strings (3-8)',
            'suggested_alternatives' => 'array of strings (5-10)',
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
            'description',
            'content',
            'pros',
            'cons',
            'features',
            'faq',
            'pricing',
            'platforms',
            'suggested_tags',
            'suggested_alternatives',
            'meta_title',
            'meta_description',
        ];
    }
}

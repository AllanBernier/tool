<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GenerationStatus;
use App\Http\Controllers\Controller;
use App\Jobs\FetchToolLogo;
use App\Jobs\GenerateComparisonContent;
use App\Jobs\GenerateToolContent;
use App\Jobs\SuggestAlternativesAndComparisons;
use App\Models\Comparison;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;

class GenerationController extends Controller
{
    public function generateTool(Tool $tool): RedirectResponse
    {
        if ($tool->generation_status === GenerationStatus::Generating) {
            return back()->with('error', 'La génération est déjà en cours.');
        }

        GenerateToolContent::dispatch($tool);

        return back()->with('success', 'Génération du contenu lancée.');
    }

    public function generateComparison(Comparison $comparison): RedirectResponse
    {
        if ($comparison->generation_status === GenerationStatus::Generating) {
            return back()->with('error', 'La génération est déjà en cours.');
        }

        GenerateComparisonContent::dispatch($comparison);

        return back()->with('success', 'Génération du comparatif lancée.');
    }

    public function suggestAlternatives(Tool $tool): RedirectResponse
    {
        SuggestAlternativesAndComparisons::dispatch($tool);

        return back()->with('success', 'Suggestion d\'alternatives lancée.');
    }

    public function fetchLogo(Tool $tool): RedirectResponse
    {
        FetchToolLogo::dispatch($tool);

        return back()->with('success', 'Récupération du logo lancée.');
    }
}

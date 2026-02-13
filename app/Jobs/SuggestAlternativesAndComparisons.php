<?php

namespace App\Jobs;

use App\AI\Prompts\SuggestAlternativesPrompt;
use App\Enums\GenerationStatus;
use App\Models\Comparison;
use App\Models\Tool;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

use function Laravel\Ai\agent;

class SuggestAlternativesAndComparisons implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(public Tool $tool) {}

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = agent(
                instructions: SuggestAlternativesPrompt::system(),
            )->prompt(
                prompt: SuggestAlternativesPrompt::user($this->tool),
            );

            $data = json_decode((string) $response, true);

            if (! is_array($data) || ! isset($data['alternatives'])) {
                throw new \RuntimeException('AI response is not valid JSON or missing alternatives.');
            }

            $alternativesCreated = 0;
            $comparisonsCreated = 0;

            foreach ($data['alternatives'] as $alternative) {
                $name = trim($alternative['name'] ?? '');
                $url = trim($alternative['url'] ?? '');

                if ($name === '') {
                    continue;
                }

                $alternativeTool = Tool::query()
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                    ->first();

                if (! $alternativeTool) {
                    $alternativeTool = Tool::create([
                        'name' => $name,
                        'url' => $url,
                        'category_id' => $this->tool->category_id,
                        'generation_status' => GenerationStatus::Pending,
                    ]);
                    $alternativesCreated++;
                }

                if ($alternativeTool->id === $this->tool->id) {
                    continue;
                }

                $this->tool->alternatives()->syncWithoutDetaching([$alternativeTool->id]);

                $comparisonExists = Comparison::query()
                    ->where(function ($query) use ($alternativeTool) {
                        $query->where('tool_a_id', $this->tool->id)
                            ->where('tool_b_id', $alternativeTool->id);
                    })
                    ->orWhere(function ($query) use ($alternativeTool) {
                        $query->where('tool_a_id', $alternativeTool->id)
                            ->where('tool_b_id', $this->tool->id);
                    })
                    ->exists();

                if (! $comparisonExists) {
                    Comparison::create([
                        'tool_a_id' => $this->tool->id,
                        'tool_b_id' => $alternativeTool->id,
                        'generation_status' => GenerationStatus::Pending,
                    ]);
                    $comparisonsCreated++;
                }
            }

            Log::info('SuggestAlternativesAndComparisons completed', [
                'tool' => $this->tool->slug,
                'alternatives_created' => $alternativesCreated,
                'comparisons_created' => $comparisonsCreated,
            ]);
        } catch (\Throwable $e) {
            Log::error('SuggestAlternativesAndComparisons failed', [
                'tool' => $this->tool->slug,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

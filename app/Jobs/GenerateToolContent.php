<?php

namespace App\Jobs;

use App\AI\Prompts\GenerateToolPrompt;
use App\Enums\GenerationStatus;
use App\Models\Tag;
use App\Models\Tool;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function Laravel\Ai\agent;

class GenerateToolContent implements ShouldQueue
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
        $this->tool->update(['generation_status' => GenerationStatus::Generating]);

        try {
            $response = agent(
                instructions: GenerateToolPrompt::system(),
            )->prompt(
                prompt: GenerateToolPrompt::user($this->tool),
            );

            $data = json_decode((string) $response, true);

            if (! is_array($data)) {
                throw new \RuntimeException('AI response is not valid JSON.');
            }

            $this->tool->update([
                'description' => $data['description'] ?? $this->tool->description,
                'content' => $data['content'] ?? $this->tool->content,
                'pros' => $data['pros'] ?? $this->tool->pros,
                'cons' => $data['cons'] ?? $this->tool->cons,
                'features' => $data['features'] ?? $this->tool->features,
                'faq' => $data['faq'] ?? $this->tool->faq,
                'pricing' => $data['pricing'] ?? $this->tool->pricing,
                'platforms' => $data['platforms'] ?? $this->tool->platforms,
                'meta_title' => $data['meta_title'] ?? $this->tool->meta_title,
                'meta_description' => $data['meta_description'] ?? $this->tool->meta_description,
                'generation_status' => GenerationStatus::Completed,
                'generated_at' => now(),
            ]);

            $this->attachSuggestedTags($data['suggested_tags'] ?? []);

            FetchToolLogo::dispatch($this->tool);
            SuggestAlternativesAndComparisons::dispatch($this->tool);
        } catch (\Throwable $e) {
            $this->tool->update(['generation_status' => GenerationStatus::Failed]);

            Log::error('GenerateToolContent failed', [
                'tool' => $this->tool->slug,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Attach suggested tags to the tool, creating them if they don't exist.
     *
     * @param  array<int, string>  $tagNames
     */
    private function attachSuggestedTags(array $tagNames): void
    {
        $tagIds = [];

        foreach ($tagNames as $name) {
            $slug = Str::slug($name);

            if ($slug === '') {
                continue;
            }

            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            );

            $tagIds[] = $tag->id;
        }

        $this->tool->tags()->syncWithoutDetaching($tagIds);
    }
}

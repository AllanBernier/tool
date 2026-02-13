<?php

namespace App\Jobs;

use App\AI\Prompts\GenerateComparisonPrompt;
use App\Enums\GenerationStatus;
use App\Models\Comparison;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

use function Laravel\Ai\agent;

class GenerateComparisonContent implements ShouldQueue
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
    public function __construct(public Comparison $comparison) {}

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
        $this->comparison->update(['generation_status' => GenerationStatus::Generating]);

        try {
            $response = agent(
                instructions: GenerateComparisonPrompt::system(),
            )->prompt(
                prompt: GenerateComparisonPrompt::user($this->comparison),
            );

            $data = json_decode((string) $response, true);

            if (! is_array($data)) {
                throw new \RuntimeException('AI response is not valid JSON.');
            }

            $this->comparison->update([
                'content' => $data['content'] ?? $this->comparison->content,
                'verdict' => $data['verdict'] ?? $this->comparison->verdict,
                'meta_title' => $data['meta_title'] ?? $this->comparison->meta_title,
                'meta_description' => $data['meta_description'] ?? $this->comparison->meta_description,
                'generation_status' => GenerationStatus::Completed,
                'generated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->comparison->update(['generation_status' => GenerationStatus::Failed]);

            Log::error('GenerateComparisonContent failed', [
                'comparison' => $this->comparison->slug,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

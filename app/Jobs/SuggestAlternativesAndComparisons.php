<?php

namespace App\Jobs;

use App\Models\Tool;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
     * Execute the job.
     *
     * @todo Implement in spec 018.
     */
    public function handle(): void
    {
        // Will be implemented in spec 018.
    }
}

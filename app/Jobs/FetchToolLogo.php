<?php

namespace App\Jobs;

use App\Models\Tool;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FetchToolLogo implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;

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
        return [5, 15, 30];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $imageContent = $this->fetchFaviconFromSite() ?? $this->fetchFromGoogleApi();

        if ($imageContent === null) {
            return;
        }

        $filename = $this->tool->slug.'.png';

        Storage::disk('logos')->put($filename, $imageContent);

        $this->tool->update(['logo_path' => $filename]);
    }

    /**
     * Attempt to fetch the favicon directly from the tool's website.
     */
    private function fetchFaviconFromSite(): ?string
    {
        try {
            $response = Http::timeout(10)->get($this->tool->url);

            if ($response->failed()) {
                return null;
            }

            $iconUrl = $this->extractFaviconUrl($response->body(), $this->tool->url);

            if ($iconUrl === null) {
                return null;
            }

            $iconResponse = Http::timeout(10)->get($iconUrl);

            if ($iconResponse->failed()) {
                return null;
            }

            return $iconResponse->body();
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch favicon from site', [
                'tool' => $this->tool->slug,
                'url' => $this->tool->url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Extract the favicon URL from HTML content.
     */
    private function extractFaviconUrl(string $html, string $baseUrl): ?string
    {
        if (preg_match('/<link[^>]*rel=["\'](?:icon|shortcut icon|apple-touch-icon)["\'][^>]*href=["\']([^"\']+)["\'][^>]*\/?>/i', $html, $matches)) {
            return $this->resolveUrl($matches[1], $baseUrl);
        }

        if (preg_match('/<link[^>]*href=["\']([^"\']+)["\'][^>]*rel=["\'](?:icon|shortcut icon|apple-touch-icon)["\'][^>]*\/?>/i', $html, $matches)) {
            return $this->resolveUrl($matches[1], $baseUrl);
        }

        return null;
    }

    /**
     * Resolve a potentially relative URL against a base URL.
     */
    private function resolveUrl(string $url, string $baseUrl): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $parsed = parse_url($baseUrl);
        $base = $parsed['scheme'].'://'.$parsed['host'];

        if (str_starts_with($url, '//')) {
            return $parsed['scheme'].':'.$url;
        }

        if (str_starts_with($url, '/')) {
            return $base.$url;
        }

        return $base.'/'.$url;
    }

    /**
     * Fallback: fetch favicon from Google Favicon API.
     */
    private function fetchFromGoogleApi(): ?string
    {
        try {
            $domain = parse_url($this->tool->url, PHP_URL_HOST);

            $response = Http::timeout(10)->get('https://www.google.com/s2/favicons', [
                'domain' => $domain,
                'sz' => 128,
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->body();
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch favicon from Google API', [
                'tool' => $this->tool->slug,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}

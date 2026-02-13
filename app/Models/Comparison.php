<?php

namespace App\Models;

use App\Enums\GenerationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Comparison extends Model
{
    /** @use HasFactory<\Database\Factories\ComparisonFactory> */
    use HasFactory, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tool_a_id',
        'tool_b_id',
        'slug',
        'content',
        'verdict',
        'generation_status',
        'is_published',
        'meta_title',
        'meta_description',
        'generated_at',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'generation_status' => GenerationStatus::class,
            'is_published' => 'boolean',
            'generated_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Comparison $comparison) {
            if (empty($comparison->slug)) {
                $toolA = $comparison->toolA ?? Tool::find($comparison->tool_a_id);
                $toolB = $comparison->toolB ?? Tool::find($comparison->tool_b_id);

                if ($toolA && $toolB) {
                    $comparison->slug = Str::slug($toolA->name).'-vs-'.Str::slug($toolB->name);
                }
            }
        });

        static::saved(function () {
            Cache::forget('home:trending_comparisons');
            Cache::forget('sitemap');
        });

        static::deleted(function () {
            Cache::forget('home:trending_comparisons');
            Cache::forget('sitemap');
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope to filter published comparisons.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * @return BelongsTo<Tool, $this>
     */
    public function toolA(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_a_id');
    }

    /**
     * @return BelongsTo<Tool, $this>
     */
    public function toolB(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_b_id');
    }
}

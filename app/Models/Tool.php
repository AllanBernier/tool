<?php

namespace App\Models;

use App\Enums\GenerationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Tool extends Model
{
    /** @use HasFactory<\Database\Factories\ToolFactory> */
    use HasFactory, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'url',
        'logo_path',
        'description',
        'content',
        'pricing',
        'pros',
        'cons',
        'features',
        'faq',
        'platforms',
        'category_id',
        'is_published',
        'is_sponsored',
        'sponsored_until',
        'generation_status',
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
            'pricing' => 'array',
            'pros' => 'array',
            'cons' => 'array',
            'features' => 'array',
            'faq' => 'array',
            'platforms' => 'array',
            'is_published' => 'boolean',
            'is_sponsored' => 'boolean',
            'generation_status' => GenerationStatus::class,
            'generated_at' => 'datetime',
            'published_at' => 'datetime',
            'sponsored_until' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Tool $tool) {
            if (empty($tool->slug)) {
                $tool->slug = Str::slug($tool->name);
            }
        });

        static::updating(function (Tool $tool) {
            if ($tool->isDirty('name') && ! $tool->isDirty('slug')) {
                $tool->slug = Str::slug($tool->name);
            }
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
     * Get the logo URL with fallback to placeholder.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            return Storage::disk('logos')->url($this->logo_path);
        }

        return '/images/placeholder-logo.svg';
    }

    /**
     * Scope to filter published tools.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to filter sponsored tools.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeSponsored($query)
    {
        return $query->where('is_sponsored', true)
            ->where('sponsored_until', '>', now());
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tool_tag');
    }

    /**
     * @return BelongsToMany<self, $this>
     */
    public function alternatives(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'tool_alternatives', 'tool_id', 'alternative_id');
    }

    /**
     * @return HasMany<Comparison, $this>
     */
    public function comparisonsAsToolA(): HasMany
    {
        return $this->hasMany(Comparison::class, 'tool_a_id');
    }

    /**
     * @return HasMany<Comparison, $this>
     */
    public function comparisonsAsToolB(): HasMany
    {
        return $this->hasMany(Comparison::class, 'tool_b_id');
    }
}

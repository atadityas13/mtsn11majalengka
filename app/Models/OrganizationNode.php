<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class OrganizationNode extends Model
{
    protected $fillable = [
        'title',
        'name',
        'photo',
        'parent_id',
        'lane',
        'slug',
        'description',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (OrganizationNode $node): void {
            if (blank($node->slug)) {
                $base = Str::slug($node->title ?: 'jabatan');
                $slug = $base;
                $i = 1;
                while (static::query()
                    ->where('slug', $slug)
                    ->when($node->exists, fn ($q) => $q->where('id', '!=', $node->id))
                    ->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $node->slug = $slug;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function initials(): string
    {
        $source = $this->name ?: $this->title;
        $parts = preg_split('/\s+/', trim($source)) ?: [];

        return Str::upper(Str::substr($parts[0] ?? 'M', 0, 1).Str::substr($parts[1] ?? '', 0, 1));
    }
}

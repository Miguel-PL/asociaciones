<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSite;
use App\Models\Concerns\HasSlug;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'slug', 'excerpt', 'body', 'cover_image', 'category_id', 'is_published', 'published_at'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use BelongsToSite, HasFactory, HasSlug;

    /**
     * La noticia pertenece a una categoria del mismo sitio.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Solo las noticias visibles al público, de la más reciente a la más antigua.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->latest('published_at');
    }

    /**
     * URL de la imagen de portada, o null si la noticia no tiene.
     *
     * Absoluta a proposito: Open Graph no admite rutas relativas, y el host
     * correcto es el del sitio que se esta sirviendo, no el de APP_URL.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (blank($this->cover_image)) {
            return null;
        }

        return url(Storage::disk('public')->url($this->cover_image));
    }

    /**
     * Las noticias se localizan por su slug dentro del sitio activo.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}

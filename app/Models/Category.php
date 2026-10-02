<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSite;
use App\Models\Concerns\HasSlug;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use BelongsToSite, HasFactory, HasSlug;

    /**
     * La categoria se identifica por su nombre.
     */
    protected function slugSource(): string
    {
        return (string) $this->name;
    }

    /**
     * Las categorias se localizan por su slug, no por su id.
     *
     * Igual que Post y Page: el id es un numero interno que no debe aparecer en
     * las URLs publicas.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Noticias de la categoria.
     *
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

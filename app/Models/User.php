<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Sites\Site;
use App\Sites\SiteManager;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['site_id', 'name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Cada sitio tiene su propio panel, asi que una cuenta solo entra al
     * panel de la asociacion a la que pertenece. El id del panel es el slug
     * del sitio.
     *
     * Filament responde 403 cuando esto devuelve false, de modo que una
     * cuenta de otra asociacion ni llega a ver el panel ajeno.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->belongsToSite($panel->getId());
    }

    public function belongsToSite(string $slug): bool
    {
        return $this->site_id !== null && $this->site_id === $slug;
    }

    /**
     * Asociacion a la que pertenece la cuenta, si sigue registrada.
     */
    public function site(): ?Site
    {
        return $this->site_id
            ? app(SiteManager::class)->get($this->site_id)
            : null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

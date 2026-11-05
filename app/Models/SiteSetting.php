<?php

namespace App\Models;

use App\Sites\Site;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'site_id',
    'footer_title',
    'address',
    'email',
    'phone',
    'facebook_url',
    'instagram_url',
    'x_url',
])]
class SiteSetting extends Model
{
    /**
     * Busca la configuración del sitio y, antes de guardarla por primera vez,
     * presenta como valores iniciales los datos configurados hasta ahora.
     */
    public static function forSite(Site $site): self
    {
        $social = $site->social;

        return static::query()->firstOrNew(
            ['site_id' => $site->slug],
            [
                'footer_title' => $site->contact['title'] ?? 'Estamos en Caudete',
                'address' => $site->contact['address'] ?? null,
                'email' => $site->contact['email'] ?? null,
                'phone' => $site->contact['phone'] ?? null,
                'facebook_url' => $social['facebook'] ?? null,
                'instagram_url' => $social['instagram'] ?? null,
                'x_url' => $social['x'] ?? $social['twitter'] ?? null,
            ],
        );
    }

    /** @return array{title: string, address: ?string, email: ?string, phone: ?string} */
    public function footerContact(): array
    {
        return [
            'title' => $this->footer_title,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    /** @return array<string, string> */
    public function socialLinks(): array
    {
        return array_filter([
            'facebook' => $this->facebook_url,
            'instagram' => $this->instagram_url,
            'x' => $this->x_url,
        ], fn (?string $url): bool => filled($url));
    }
}

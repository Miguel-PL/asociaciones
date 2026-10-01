<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;

/**
 * Login acotado al sitio del panel.
 *
 * Añade site_id a las credenciales, de modo que Auth::attempt solo valida
 * cuentas de esa asociacion. Una cuenta de otro sitio que se escriba aqui
 * falla con "credenciales incorrectas" en vez de autenticarse y saltar un 403
 * despues, que es lo que ocurriria sin esto.
 */
class Login extends BaseLogin
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return parent::getCredentialsFromFormData($data) + [
            'site_id' => Filament::getCurrentPanel()?->getId(),
        ];
    }
}

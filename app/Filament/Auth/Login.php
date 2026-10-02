<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;

/**
 * Login acotado al sitio del panel.
 *
 * Añade site_id a las credenciales, de modo que Auth::attempt solo valida
 * cuentas de esa asociacion. Una cuenta de otro sitio que se escriba aqui
 * falla con "credenciales incorrectas" en vez de autenticarse y saltar un 403
 * despues, que es lo que ocurriria sin esto.
 *
 * La cuenta de administracion de la plataforma es la excepcion: no pertenece
 * a ninguna asociacion, asi que no se le puede filtrar por site_id y entra en
 * todos los paneles. El filtro se decide por el correo tecleado, no por el
 * panel, y el mensaje de error es el mismo en todos los casos: escritura esto
 * no revela que cuentas existen.
 */
class Login extends BaseLogin
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $credentials = parent::getCredentialsFromFormData($data);

        $siteId = Filament::getCurrentPanel()?->getId();

        if ($siteId !== null && ! $this->esSuperAdmin($credentials['email'] ?? null)) {
            $credentials['site_id'] = $siteId;
        }

        return $credentials;
    }

    /**
     * @param  mixed  $email
     */
    protected function esSuperAdmin($email): bool
    {
        return filled($email) && User::query()
            ->where('email', $email)
            ->where('is_super_admin', true)
            ->exists();
    }
}

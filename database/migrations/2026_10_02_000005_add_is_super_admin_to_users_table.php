<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cuenta de administracion de toda la plataforma.
     *
     * is_super_admin no sustituye a site_id: solo levanta la restriccion de
     * entrar en un panel ajeno. La cuenta sigue sin pertenecer a ninguna
     * asociacion, asi que dentro de cada panel ve el contenido de ese sitio y
     * no el de los demas.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_super_admin')->default(false)->after('site_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_super_admin');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cada panel es el de una asociacion y solo acepta cuentas de esa
     * asociacion, asi que la cuenta necesita saber a que sitio pertenece.
     *
     * Se deja nullable para no romper instalaciones existentes; el acceso al
     * panel se deniega cuando site_id esta vacio.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('site_id')->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('site_id');
        });
    }
};

<?php

namespace App\Providers;

use App\Models\Empresa;
use App\Models\PageWeb;
use App\Models\LicenciaUsuario; // Asegúrate de importar el modelo
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema; // Para verificar si la tabla existe
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         // Compartir el nombre de page_web con todas las vistas si la tabla existe
         if (Schema::hasTable('page_web')) {
            $pageWeb = PageWeb::first();
            View::share('pageWeb', $pageWeb);
        }

        // Verificar si la tabla 'empresa' existe
        if (Schema::hasTable('empresa')) {
            // Obtener el primer registro de la tabla 'empresa' donde 'img_account' no sea nulo ni vacío
            $empresa = Empresa::whereNotNull('img_account')
                ->where('img_account', '!=', '')
                ->first();

            // Si se encontró un registro con 'img_account' válido, compartirlo con las vistas
            if ($empresa && isset($pageWeb)) {
                $imgAccount = rtrim($pageWeb->url_sistem, '/') . '/storage/img_account_empresa/' . $empresa->img_account;
                View::share('imgAccount', $imgAccount);
            }
        }
    }
}
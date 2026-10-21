<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicenciaController;

Route::post('test', function () {
    return response()->json(['message' => 'Ruta de prueba funcionando']);
});

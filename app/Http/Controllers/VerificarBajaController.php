<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class VerificarBajaController extends Controller
{
    //
    public function runCommand()
    {
        Artisan::call('comprobantes:verificar-baja');
        return response()->json(['status' => 'Comando ejecutado']);
    }
}

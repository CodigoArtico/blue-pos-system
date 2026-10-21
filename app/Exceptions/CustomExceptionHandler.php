<?php

namespace App\Exceptions;

use Exception;

class CustomExceptionHandler extends Exception
{
    //
    public function render($request)
    {
        // Retornar la vista personalizada de error
        return response()->view('errors.403', [], 403);
    }
}

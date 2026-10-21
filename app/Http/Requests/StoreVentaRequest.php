<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha_hora' => 'required',
            'impuesto' => 'required',
            'numero_comprobante' => 'required|string|max:255',
            'identificador_web' => 'nullable', // campo no requerido 
            'total' => 'required|numeric',
            'cliente_id' => 'required|exists:clientes,id',
            'user_id' => 'required|exists:users,id',
            'comprobante_id' => 'required|exists:comprobantes,id',
            'metodo_pagos' => 'required|exists:metodo_pagos,id', // Validar la tabla método_pagos
            'monto_recibido' => 'nullable', //  --|numeric-- Campo no requerido y debe ser numérico
            'numero_operacion' => 'nullable', // campo no requerido 
        ];
    }
}

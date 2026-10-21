<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Persona; // Importación correcta

class StoreProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajusta según tu lógica de autorización
    }

    public function rules()
    {
        return [
            'documento_id' => 'required|exists:documentos,id',
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    // Verificar si existe una Persona con el mismo numero_documento asociada a un Proveedor
                    $exists = Persona::where('numero_documento', $value)
                        ->whereHas('proveedor')
                        ->exists();

                    if ($exists) {
                        $fail('Ya existe un documento en un proveedor.');
                    }
                },
            ],
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'tipo_persona' => 'required|in:natural,juridica',
        ];
    }

    public function messages()
    {
        return [
            'documento_id.required' => 'El tipo de documento es obligatorio.',
            'documento_id.exists' => 'El tipo de documento seleccionado no es válido.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            // Remueve o ajusta este mensaje ya que ahora usamos una validación personalizada
            // 'numero_documento.unique' => 'El número de documento ya está registrado.',
            'razon_social.required' => 'Este campo es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'tipo_persona.required' => 'El tipo de persona es obligatorio.',
            'tipo_persona.in' => 'El tipo de persona seleccionado no es válido.',
        ];
    }
}

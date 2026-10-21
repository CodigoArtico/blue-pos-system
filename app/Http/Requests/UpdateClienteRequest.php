<?php

namespace App\Http\Requests;

use App\Models\Persona;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambia esto según tu lógica de autorización
    }

    public function rules()
    {
        $personaId = $this->route('cliente')->persona->id;

        return [
            'documento_id' => 'required|exists:documentos,id',
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    // Obtén el ID de la persona actual, que puede estar asociada a un cliente
                    $personaIdActual = $this->cliente->persona->id;

                    // Verificar si existe una Persona con el mismo numero_documento asociada a un Cliente, pero ignorando el ID actual
                    $exists = Persona::where('numero_documento', $value)
                        ->whereHas('cliente')
                        ->where('id', '!=', $personaIdActual) // Ignorar el ID actual
                        ->exists();

                    if ($exists) {
                        $fail('El documento ya tiene un cliente.');
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
            'numero_documento.unique' => 'El número de documento ya está registrado.',
            'razon_social.required' => 'Este campo es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'tipo_persona.required' => 'El tipo de persona es obligatorio.',
            'tipo_persona.in' => 'El tipo de persona seleccionado no es válido.',
        ];
    }
}

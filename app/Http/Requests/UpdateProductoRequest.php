<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
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
        $producto = $this->route('producto');
        return [
            'codigo' => 'required|unique:productos,codigo,' . $producto->id . '|max:50',
            'nombre' => 'required|unique:productos,nombre,' . $producto->id . '|max:150',
            'descripcion' => 'nullable|max:255',
            'fecha_vencimiento' => 'nullable|date',
            'img_path' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'img_url' => 'nullable',
            'medida' => 'nullable',
            'marca_id' => 'required|integer|exists:marcas,id',
            'presentacione_id' => 'required|integer|exists:presentaciones,id',
            'categorias' => 'required',
            'destacado.*' => 'nullable',
        ];
    }
    public function attributes()
    {
        return [
            'marca_id' => 'marca',
            'presentacione_id' => 'presentación'
        ];
    }

    public function messages()
    {
        return [
            'codigo' => 'Se necesita un codigo'
        ];
    }
}

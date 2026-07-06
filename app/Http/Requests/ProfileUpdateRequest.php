<?php

namespace App\Http\Requests;

use Modules\Access\Models\Usuario;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre'    => ['required', 'string', 'max:100'],
            'apellido'  => ['required', 'string', 'max:100'],
            'email'     => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Usuario::class, 'email')->ignore($this->user()->ci, 'ci'),
            ],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'sexo'      => ['nullable', 'in:M,F'],
            'domicilio' => ['nullable', 'string', 'max:255'],
        ];
    }
}

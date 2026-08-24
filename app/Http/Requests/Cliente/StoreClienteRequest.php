<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['nullable', 'array', 'min:1'],
            'roles.*' => ['required', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'El correo electrónico ya se encuentra registrado.',
            'email.email' => 'Debe ingresar un formato de correo electrónico válido.',
            'password.min' => 'La contraseña debe contener al menos 8 caracteres.',
            'roles.*.exists' => 'Uno o más roles seleccionados no son válidos.',
        ];
    }
}

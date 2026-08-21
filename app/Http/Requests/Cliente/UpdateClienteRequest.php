<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
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
        $clienteId  = $this->route('cliente')?->id;

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')
                    ->ignore($clienteId)
                    ->where(function ($query) {
                        return $query->whereRaw('BINARY email = ?', [$this->email]);
                    }),
            ],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'roles' => ['required','array', 'min:1'],
            'roles.*' => ['required', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'El correo electrónico ya se encuentra registrado por otro usuario.',
            'email.email' => 'Debe ingresar un formato de correo electrónico válido.',
            'password.min' => 'La nueva contraseña debe contener al menos 8 caracteres.',
            'roles.*.exists' => 'Uno o más roles seleccionados no son válidos.',
        ];
    }
}

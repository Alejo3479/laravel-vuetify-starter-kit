<?php

namespace App\Http\Requests\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
            'name' => ['required', 'string','min:1', 'max:255',
                Rule::unique('roles', 'name')->where(function ($query) {
                        return $query->whereRaw('BINARY name = ?', [$this->name]);
                    }),
                ],
            'permissions' => ['array'],
            'permissions.*' => ['required', 'exists:permissions,id'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.unique' => 'El nombre de este rol ya se encuentra registrado',
            'permissions.*.exists' => 'Uno o más permisos seleccionados no son válidos.',
        ];
    }
}

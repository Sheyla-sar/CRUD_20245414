<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class PatchUserRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            // Sometimes: solo valida el campo si está presente en el formulario que recibe
            // String: debe ser texto
            // date: debe ser una fecha 
            // email: debe ser un email válido en formato @ dominio
            // max: el número máximo de caractere
            // Rule::unique('users'): el username debe ser único en la tabla "users"
            // ->ignore($userID): hace que se ignore el usuario actual para que se pueda actualizar sin dar error
            // regex:/^\d{8}-\d$/: debe tener 8 d(ígitos), un espacio y luego un último d(ígito)
            // regex:/^\+\d{1,4}\s?\d{4}-?\d{4}$/: un formato de +503 XXXX-XXXX
            // before:today : debe haber nacido en el pasado y no el futuro

            'name' => ['sometimes', 'string', 'max:255'], 
            'lastname' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'hiring_date' => ['sometimes', 'date'],
            'dui' => ['sometimes', 'string', Rule::unique('users', 'dui')->ignore($userId), 'regex:/^\d{8}-\d$/'],
            'phone_number' => ['sometimes', 'string', 'regex:/^\+\d{1,4}\s?\d{4}-?\d{4}$/'],
            'birth_date' => ['sometimes', 'date', 'before:today']
        ];
    }
}

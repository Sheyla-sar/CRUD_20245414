<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class UpdateUserRequest extends FormRequest
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

            'name' => ['required', 'string', 'max:255'], 
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'hiring_date' => ['required', 'date'],
            'dui' => ['required', 'string', Rule::unique('users', 'dui')->ignore($userId), 'regex:/^\d{8}-\d$/'],
            'phone_number' => ['required', 'string', 'regex:/^\+\d{1,4}\s?\d{4}-?\d{4}$/'],
            'birth_date' => ['required', 'date', 'before:today']
        ];
    }
}

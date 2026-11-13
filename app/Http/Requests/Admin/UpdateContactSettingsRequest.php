<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateContactSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s.]{6,30}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.email' => 'El correo de contacto no es válido.',
            'phone.regex' => 'El teléfono solo admite números, espacios, paréntesis, puntos, guiones y el signo +.',
        ];
    }
}

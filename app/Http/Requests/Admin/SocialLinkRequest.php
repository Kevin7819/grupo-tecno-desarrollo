<?php

namespace App\Http\Requests\Admin;

use App\Domain\Cms\SocialLink;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class SocialLinkRequest extends FormRequest
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
            'label' => ['required', 'string', 'max:60'],
            'url' => ['required', 'string', 'max:2048', 'url'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Only https:// URLs are published: anything else is rejected instead of
     * being stored and silently ignored later.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $url = trim((string) $this->input('url'));

                if ($url === '' || $validator->errors()->has('url')) {
                    return;
                }

                if (! SocialLink::isValidUrl($url)) {
                    $validator->errors()->add(
                        'url',
                        'El enlace debe ser una URL completa que empiece con https://.',
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'label.required' => 'Escribí el nombre de la red o del canal.',
            'url.required' => 'Escribí el enlace del perfil.',
            'url.url' => 'El enlace no es una URL válida.',
        ];
    }
}

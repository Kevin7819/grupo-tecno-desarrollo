<?php

namespace App\Http\Requests\Admin;

use App\Domain\Radio\StreamFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class UpdateStationSettingsRequest extends FormRequest
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
            'station_name' => ['nullable', 'string', 'max:120'],
            'slogan' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'stream_url' => ['nullable', 'string', 'max:2048', 'url'],
            'stream_format' => ['required', Rule::in(array_map(static fn (StreamFormat $format): string => $format->value, StreamFormat::cases()))],
            'stream_name' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(['active', 'maintenance', 'inactive'])],
        ];
    }

    /**
     * The player must never be pointed at a URL the browser cannot play in
     * this environment. Plain http is accepted only during local development.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $url = trim((string) $this->input('stream_url'));

                if ($url === '' || $validator->errors()->has('stream_url')) {
                    return;
                }

                $scheme = mb_strtolower((string) parse_url($url, PHP_URL_SCHEME));
                $host = (string) parse_url($url, PHP_URL_HOST);
                $allowed = $scheme === 'https' || ($scheme === 'http' && app()->environment('local'));

                if (! $allowed || $host === '') {
                    $validator->errors()->add(
                        'stream_url',
                        'La URL de la transmisión debe usar https://. http:// solo se acepta en el entorno local.',
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
            'logo.image' => 'El logotipo debe ser una imagen.',
            'logo.mimes' => 'El logotipo debe ser un archivo JPG, PNG o WebP.',
            'logo.max' => 'El logotipo no puede superar los 2 MB.',
            'stream_format.required' => 'Elegí el formato de la transmisión.',
            'status.required' => 'Elegí el estado de la emisora.',
        ];
    }
}

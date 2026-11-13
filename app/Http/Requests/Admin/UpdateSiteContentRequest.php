<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateSiteContentRequest extends FormRequest
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
            'hero_kicker' => ['nullable', 'string', 'max:160'],
            'hero_title' => ['nullable', 'string', 'max:160'],
            'hero_intro' => ['nullable', 'string', 'max:1000'],
            'about_title' => ['nullable', 'string', 'max:160'],
            'about_text' => ['nullable', 'string', 'max:2000'],
            'gallery_title' => ['nullable', 'string', 'max:160'],
            'gallery_intro' => ['nullable', 'string', 'max:1000'],
            'contact_title' => ['nullable', 'string', 'max:160'],
            'contact_intro' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

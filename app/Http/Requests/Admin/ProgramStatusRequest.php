<?php

namespace App\Http\Requests\Admin;

use App\Domain\Cms\ProgramStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProgramStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in(array_map(static fn (ProgramStatus $status): string => $status->value, ProgramStatus::cases()))],
        ];
    }
}

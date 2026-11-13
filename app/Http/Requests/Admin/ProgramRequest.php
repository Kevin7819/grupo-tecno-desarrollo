<?php

namespace App\Http\Requests\Admin;

use App\Domain\Cms\ProgramStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    /**
     * Empty schedule rows (added by the form and left blank) are ignored
     * instead of failing validation as incomplete rows.
     */
    protected function prepareForValidation(): void
    {
        $schedule = $this->input('schedule');

        if (! is_array($schedule)) {
            return;
        }

        $schedule = array_values(array_filter($schedule, static function ($row): bool {
            if (! is_array($row)) {
                return false;
            }

            foreach (['weekday', 'starts_at', 'ends_at'] as $field) {
                if (trim((string) ($row[$field] ?? '')) !== '') {
                    return true;
                }
            }

            return false;
        }));

        $this->merge(['schedule' => $schedule]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(array_map(static fn (ProgramStatus $status): string => $status->value, ProgramStatus::cases()))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'schedule' => ['nullable', 'array', 'max:20'],
            'schedule.*.weekday' => ['required', 'integer', 'between:1,7'],
            'schedule.*.starts_at' => ['required', 'date_format:H:i'],
            'schedule.*.ends_at' => ['required', 'date_format:H:i'],
            'hosts' => ['nullable', 'array'],
            'hosts.*' => ['integer', 'exists:hosts,id'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                foreach ((array) $this->input('schedule', []) as $index => $row) {
                    $startsAt = (string) ($row['starts_at'] ?? '');
                    $endsAt = (string) ($row['ends_at'] ?? '');

                    if ($startsAt === '' || $endsAt === '' || $startsAt >= $endsAt) {
                        $validator->errors()->add(
                            "schedule.{$index}.ends_at",
                            'La hora de fin debe ser posterior a la hora de inicio. Para una franja que cruza la medianoche, registrá dos franjas.',
                        );
                    }
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
            'name.required' => 'El programa necesita un nombre.',
            'cover.image' => 'La portada debe ser una imagen.',
            'cover.mimes' => 'La portada debe ser un archivo JPG, PNG o WebP.',
            'cover.max' => 'La portada no puede superar los 2 MB.',
            'schedule.*.weekday.required' => 'Cada franja necesita un día.',
            'schedule.*.weekday.between' => 'El día debe estar entre lunes y domingo.',
            'schedule.*.starts_at.required' => 'Cada franja necesita una hora de inicio.',
            'schedule.*.starts_at.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'schedule.*.ends_at.required' => 'Cada franja necesita una hora de fin.',
            'schedule.*.ends_at.date_format' => 'La hora de fin debe tener el formato HH:MM.',
            'hosts.*.exists' => 'El locutor elegido no existe.',
        ];
    }
}

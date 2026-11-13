<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Application\Cms\ContactRepository;
use App\Application\Cms\SocialLinkInput;
use App\Domain\Cms\ContactDetails;
use App\Domain\Cms\SocialLink as SocialLinkValue;
use App\Models\ContactSetting;
use App\Models\SocialLink as SocialLinkModel;
use Illuminate\Database\QueryException;

final class EloquentContactRepository implements ContactRepository
{
    private const CONTACT_ID = 1;

    public function current(): ContactDetails
    {
        try {
            $row = ContactSetting::query()->orderBy('id')->first();
        } catch (QueryException) {
            // The table may not exist yet on a fresh clone; the public site
            // falls back to the environment configuration.
            return ContactDetails::empty();
        }

        if ($row === null) {
            return ContactDetails::empty();
        }

        return ContactDetails::fromValues($row->email, $row->phone);
    }

    public function save(ContactDetails $details): ContactDetails
    {
        ContactSetting::query()->updateOrCreate(
            ['id' => self::CONTACT_ID],
            [
                'email' => $details->email,
                'phone' => $details->phone,
            ],
        );

        return $details;
    }

    public function socialLinks(): array
    {
        try {
            return SocialLinkModel::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map($this->toDomain(...))
                ->all();
        } catch (QueryException) {
            return [];
        }
    }

    public function activeSocialLinks(): array
    {
        try {
            return SocialLinkModel::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map($this->toDomain(...))
                ->all();
        } catch (QueryException) {
            return [];
        }
    }

    public function findSocialLink(int $id): ?SocialLinkValue
    {
        $model = SocialLinkModel::query()->find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function createSocialLink(SocialLinkInput $input): SocialLinkValue
    {
        return $this->toDomain(SocialLinkModel::query()->create($this->attributes($input)));
    }

    public function updateSocialLink(SocialLinkValue $link, SocialLinkInput $input): SocialLinkValue
    {
        $model = SocialLinkModel::query()->findOrFail($link->id);
        $model->update($this->attributes($input));

        return $this->toDomain($model);
    }

    public function deleteSocialLink(SocialLinkValue $link): void
    {
        SocialLinkModel::query()->findOrFail($link->id)->delete();
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(SocialLinkInput $input): array
    {
        return [
            'label' => $input->label,
            'url' => $input->url,
            'sort_order' => $input->sortOrder,
            'is_active' => $input->isActive,
        ];
    }

    private function toDomain(SocialLinkModel $model): SocialLinkValue
    {
        return SocialLinkValue::fromValues(
            id: $model->id,
            label: $model->label,
            url: $model->url,
            sortOrder: (int) $model->sort_order,
            isActive: (bool) $model->is_active,
        );
    }
}

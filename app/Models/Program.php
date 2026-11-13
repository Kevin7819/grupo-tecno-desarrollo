<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'cover_path', 'status', 'sort_order'])]
class Program extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsToMany<Host, $this>
     */
    public function hosts(): BelongsToMany
    {
        return $this->belongsToMany(Host::class, 'program_host');
    }

    /**
     * @return HasMany<ProgramSchedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ProgramSchedule::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'station_name',
    'slogan',
    'description',
    'logo_path',
    'stream_url',
    'stream_format',
    'stream_name',
    'status',
])]
class StationSetting extends Model
{
    /**
     * There is only one settings row; callers always use the repository.
     */
    protected $table = 'station_settings';
}

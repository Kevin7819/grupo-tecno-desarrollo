<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'hero_kicker',
    'hero_title',
    'hero_intro',
    'about_title',
    'about_text',
    'gallery_title',
    'gallery_intro',
    'contact_title',
    'contact_intro',
])]
class SiteContent extends Model
{
    /**
     * There is only one content row; callers always use the repository.
     */
    protected $table = 'site_contents';
}

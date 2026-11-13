<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['email', 'phone'])]
class ContactSetting extends Model
{
    /**
     * There is only one contact row; callers always use the repository.
     */
    protected $table = 'contact_settings';
}

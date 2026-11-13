<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['admin_user_id', 'actor_name', 'action', 'subject'])]
class AdminActivity extends Model
{
    /**
     * An activity entry is written once and never updated.
     */
    public const UPDATED_AT = null;

    protected $table = 'admin_activities';
}

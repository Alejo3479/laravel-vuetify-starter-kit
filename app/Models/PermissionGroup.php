<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class PermissionGroup extends Model
{
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
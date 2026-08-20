<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Wijzijnweb\LaravelInertiaPermissions\App\Models\PermissionGroup;

#[Fillable(['name', 'guard_name', 'label', 'permission_group_id'])]
class Permission extends SpatiePermission
{
    use HasFactory;

    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }
}

<?php

namespace App\Models\Role;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use SoftDeletes;

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
            Cache::forget(config('cache_entity.role.list'));
        });
        self::updating(function ($model) {
            Cache::forget(config('cache_entity.role.list'));
            Cache::forget(config('cache_entity.role.entity') . $model->uuid);
        });
        self::deleting(function ($model) {
            Cache::forget(config('cache_entity.role.list'));
            Cache::forget(config('cache_entity.role.entity') . $model->uuid);
        });
    }
}

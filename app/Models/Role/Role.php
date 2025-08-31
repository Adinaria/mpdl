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
        });
        self::created(function () {
            Cache::forget(config('cache_entity.role.cache_keys.list'));
        });
        self::updated(function ($model) {
            Cache::forget(config('cache_entity.role.list'));
            Cache::forget(config('cache_entity.role.cache_keys.entity') . $model->uuid);
        });
        self::deleted(function ($model) {
            Cache::forget(config('cache_entity.role.list'));
            Cache::forget(config('cache_entity.role.cache_keys.entity') . $model->uuid);
        });
    }
}

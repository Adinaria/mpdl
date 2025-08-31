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
            self::clearCacheByRole($model);
        });
        self::deleted(function ($model) {
            self::clearCacheByRole($model);
        });
    }

    private static function clearCacheByRole(self $model): void
    {
        Cache::forget(config('cache_entity.role.list'));
        Cache::forget(config('cache_entity.role.cache_keys.entity') . $model->uuid);
        Cache::forget(config('cache_entity.user.cache_keys.list'));
        $model->users()->each(function ($user) {
            Cache::forget(config('cache_entity.user.cache_keys.entity') . $user->uuid);
        });
    }

}

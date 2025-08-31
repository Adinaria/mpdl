<?php

namespace App\Models\Role;

use App\Enums\YesNoEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use SoftDeletes;

    protected $casts = [
        'default_role' => YesNoEnum::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
            if (is_null($model->default_role)) {
                $model->default_role = YesNoEnum::No;
            }
        });
        self::created(function () {
            if (config('cache_entity.role.mode')) {
                Cache::forget(config('cache_entity.role.cache_keys.list'));
            }
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
        if (config('cache_entity.role.mode')) {
            Cache::forget(config('cache_entity.role.cache_keys.list'));
            Cache::forget(config('cache_entity.role.cache_keys.entity') . $model->uuid);

            $users = $model->users()->get();
            if ($users->isNotEmpty()) {
                Cache::forget(config('cache_entity.user.cache_keys.list'));
                $users->each(function ($user) {
                    Cache::forget(config('cache_entity.user.cache_keys.entity') . $user->uuid);
                });
            }
        }
    }

}

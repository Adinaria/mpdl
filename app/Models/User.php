<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
            Cache::forget(config('cache_entity.user.cache_keys.list'));
        });
        self::created(function () {
            Cache::forget(config('cache_entity.user.cache_keys.list'));
        });
        self::updated(function ($model) {
            self::clearCacheByUser($model);
        });
        self::deleted(function ($model) {
            self::clearCacheByUser($model);
        });
    }

    private static function clearCacheByUser(self $model): void
    {
        Cache::forget(config('cache_entity.user.list'));
        Cache::forget(config('cache_entity.user.cache_keys.entity') . $model->uuid);
    }
}

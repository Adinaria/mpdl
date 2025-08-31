<?php

namespace App\Providers;

use App\Services\Role\Repository\RoleRepository;
use App\Services\Role\Repository\RoleRepositoryInterface;
use App\Services\User\Repository\UserRepository;
use App\Services\User\Repository\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(RoleRepositoryInterface::class, RoleRepository::class);
    }
}

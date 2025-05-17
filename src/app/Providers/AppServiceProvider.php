<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Domain\Repositories\ClothesRepositoryInterface;
use App\Domain\Repositories\CoordinateRepositoryInterface;
use App\Infrastructure\Repositories\CategoryRepository;
use App\Infrastructure\Repositories\ClothesRepository;
use App\Infrastructure\Repositories\CoordinateRepository;
use App\Infrastructure\Repositories\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // リポジトリの依存関係を設定
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ClothesRepositoryInterface::class, ClothesRepository::class);
        $this->app->bind(CoordinateRepositoryInterface::class, CoordinateRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use App\Models\ParametreApplication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $branding = Cache::remember('application_branding', 300, function () {
                return Schema::hasTable('parametres_application')
                    ? ParametreApplication::query()->find(1)
                    : null;
            });

            if ($branding) {
                config(['app.name' => $branding->nom]);
            }

            $view->with('branding', $branding);
        });
    }
}

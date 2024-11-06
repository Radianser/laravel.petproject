<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        // Paginator::defaultView('vendor.pagination.tailwind');

        Password::defaults(function () {
            $rule = Password::min(6);
     
            /** @var \Illuminate\Foundation\Application $app */
            $app = $this->app;
            return $app->isProduction()
                        ? $rule->mixedCase()
                        : $rule;
        });

        Relation::morphMap([
            'babble' => 'App\Models\Babble',
            'comment' => 'App\Models\Comment',
            'image' => 'App\Models\Image',
            'video' => 'App\Models\Video'
        ]);
    }
}

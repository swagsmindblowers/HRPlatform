<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\BetterOff\Rates\EloquentRatesRepository;
use App\BetterOff\Rates\RatesRepositoryInterface;

class BetterOffServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RatesRepositoryInterface::class, EloquentRatesRepository::class);
        $this->mergeConfigFrom(__DIR__.'/../../config/betteroff.php', 'betteroff');
    }

    public function boot(): void
    {
        Route::middleware('web')
            ->namespace('App\Http\Controllers')
            ->group(base_path('routes/betteroff.php'));
    }
}

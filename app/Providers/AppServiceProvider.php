<?php

namespace App\Providers;

use App\Http\Controllers\UnresolvedTicketsByChannelsController;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TicketRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    // public function register(): void
    // {
    //     $this->app->bind(UnresolvedTicketsByChannelsController::class, function () {
    //         return new TicketRepository();
    //     });
    // }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

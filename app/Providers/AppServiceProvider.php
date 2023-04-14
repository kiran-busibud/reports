<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TicketRepository;
use App\Repositories\EmailInfo\IEmailInfoRepository;
use App\Repositories\EmailInfo\EmailInfoRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IEmailInfoRepository::class, function () {
            return new EmailInfoRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

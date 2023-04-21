<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TicketRepository;
use App\Repositories\EmailInfo\IEmailInfoRepository;
use App\Repositories\EmailInfo\EmailInfoRepository;
use App\Repositories\EmailInfo\IAttachmentRepository;
use App\Repositories\EmailInfo\AttachmentRepository;


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
        $this->app->bind(IAttachmentRepository::class, function () {
            return new AttachmentRepository();
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

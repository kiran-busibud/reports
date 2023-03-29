<?php
namespace routes\TicketRoutes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnresolvedTicketsByChannelsController;


class TicketRoutes
{
    public static function reportRoutes()
    {

        Route::group(["prefix", "unresolved"], function () {
            Route::get('/bychannels', [UnresolvedTicketsByChannelsController::class, 'getTickets']);
        });
    }
    
}
<?php
namespace routes\ReportsRoutes;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnresolvedTicketsByChannelsController;


class TicketRoutes
{
    public static function reportRoutes()
    {

        Route::group(["prefix", "unresolved"], function () {
            Route::get('/bychannels', [UnresolvedTicketsByChannelsController::class, 'getTickets']);
            Route::get('/bychannels/listview', [ReportsController::class, 'getUnresolvedTicketsByChannelsListview']);
            Route::get('/pendingtime', [ReportsController::class, 'getUnresolvedTicketsByPendingTime']);
            Route::get('/pendingtime/listview', [ReportsController::class, 'getUnresolvedTicketsByPendingTimeListview']);
            Route::get('/messages', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForth']);
            Route::get('/messages/listview', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForthListview']);
            Route::get('/notification', [ReportsController::class, 'getUnresolvedTicketsForNotification']);
            Route::get('/notificationIds', [ReportsController::class, 'getUnresolvedTicketIdsForNotification']);
        });
    }
    
}
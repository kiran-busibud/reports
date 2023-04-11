<?php
namespace app\Routes;

use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;


class ReportRoutes
{
    public static function reportRoutes()
    {

        Route::prefix('currentstatus')->group(function () {

            Route::prefix('tickets-by-channels')->group(function(){
                Route::get('/', [ReportsController::class, 'getUnresolvedTicketsByChannels']);
                Route::get('/listview', [ReportsController::class, 'getUnresolvedTicketsByChannelsListview']);
            });

            Route::prefix('tickets-by-pendingtime')->group(function(){
                Route::get('/', [ReportsController::class, 'getUnresolvedTicketsByPendingTime']);
                Route::get('/listview', [ReportsController::class, 'getUnresolvedTicketsByPendingTimeListview']);
            });
        
            Route::prefix('most-back-and-forth-tickets')->group(function(){
                Route::get('/', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForth']);
                Route::get('/listview', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForthListview']);
            });

            Route::get('/unresolved-tickets-notification', [ReportsController::class, 'getUnresolvedTicketsForNotification']);
            Route::get('/unresolve-ticket-ids-notification', [ReportsController::class, 'getUnresolvedTicketIdsForNotification']);
        });

        Route::prefix('volume-and-activity')->group(function () {

            Route::prefix('tickets-created')->group(function(){
                Route::get("/daily", [ReportsController::class, 'getTicketsByCreationTimeDaily']);
                Route::get("/weekly", [ReportsController::class, 'getTicketsByCreationTimeWeekly']);
                Route::get("/monthly", [ReportsController::class, 'getTicketsByCreationTimemonthly']);
            });

            Route::prefix('tickets-created-average')->group(function(){
                Route::get("/daily", [ReportsController::class, 'getAverageTicketCreationTimeDaily']);
                Route::get("/weekly", [ReportsController::class, 'getAverageTicketCreationTimeWeekly']);
                Route::get("/monthly", [ReportsController::class, 'getAverageTicketCreationTimeMonthly']);
            });

            Route::prefix('tickets-closed')->group(function(){
                Route::get("/daily", [ReportsController::class, 'getAverageTicketCreationTimeDaily']);
                Route::get("/weekly", [ReportsController::class, 'getAverageTicketCreationTimeWeekly']);
                Route::get("/monthly", [ReportsController::class, 'getAverageTicketCreationTimeMonthly']);
            });

            Route::prefix('backlog-percentage')->group(function(){
                Route::get("/daily", [ReportsController::class, 'getBacklogTicketsDaily']);
                Route::get("/weekly", [ReportsController::class, 'getBacklogTicketsWeekly']);
                Route::get("/monthly", [ReportsController::class, 'getBacklogTicketsMonthly']);
            });
        });

        Route::prefix('performance')->group(function () {

            Route::get('/tickets-replytime', [ReportsController::class, 'getTicketsByFirstReplyTimeBrackets']);

            Route::get("/tickets-resolutiontime", [ReportsController::class, 'getTicketsByResolutionTime']);
            
            Route::prefix('firstreplytime-average-and-median')->group(function(){
                Route::get("/daily", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeDaily']);
                Route::get("/weekly", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeWeekly']);
                Route::get("/monthly", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeMonthly']);
            });
            
            Route::prefix('resolutiontime-average-and-median')->group(function(){
                Route::get("/daily", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeDaily']);
                Route::get("/weekly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeWeekly']);
                Route::get("/monthly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeMonthly']);
            });
        });

        Route::prefix('livechats')->group(function(){

            Route::prefix('totalchats')->group(function(){
                Route::get("/daily", [ReportsController::class, 'getTotalChatsDaily']);
                Route::get("/weekly", [ReportsController::class, 'getTotalChatsWeekly']);
                Route::get("/monthly", [ReportsController::class, 'getTotalChatsMonthly']);
            });
        });
    }
    
}


<?php

use App\Http\Controllers\TicketsController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;
use routes\TicketRoutes;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/unresolved-tickets-by-channels', [TicketsController::class, 'getUnresolvedTicketsByChannels']);
// Route::get('/unresolved-tickets-by-channels-listview', [TicketsController::class, 'getUnresolvedTicketsByChannelsListView']);
// Route::get('/unresolvedTicketsByPendingTime', [TicketsController::class, 'getUnresolvedTicketsByPendingTime']);
// Route::get('/unresolvedTicketsByPendingTimeListview', [TicketsController::class, 'getUnresolvedTicketsByPendingTimeListView']);
// Route::get('/unresolvedTicketByMostBackAndForth', [TicketsController::class, 'getUnresolvedTicketsByMostBackAndForth']);

Route::get('/bychannels', [ReportsController::class, 'getUnresolvedTicketsByChannels']);
Route::get('/bychannels/listview', [ReportsController::class, 'getUnresolvedTicketsByChannelsListview']);
Route::get('/pendingtime', [ReportsController::class, 'getUnresolvedTicketsByPendingTime']);
Route::get('/pendingtime/listview', [ReportsController::class, 'getUnresolvedTicketsByPendingTimeListview']);
Route::get('/messages', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForth']);
Route::get('/messages/listview', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForthListview']);
Route::get('/tickets/notification', [ReportsController::class, 'getUnresolvedTicketsForNotification']);
Route::get('/ticketIds/notification', [ReportsController::class, 'getUnresolvedTicketIdsForNotification']);
Route::get('/tickets/replytime', [ReportsController::class, 'getTicketsByFirstReplyTimeBrackets']);
Route::get("/tickets/resolutiontime", [ReportsController::class, 'getTicketsByResolutionTime']);
Route::get("/tickets/creationtime/daily", [ReportsController::class, 'getTicketsByCreationTimeDaily']);
Route::get("/tickets/creationtime/weekly", [ReportsController::class, 'getTicketsByCreationTimeWeekly']);
Route::get("/tickets/creationtime/monthly", [ReportsController::class, 'getTicketsByCreationTimemonthly']);
Route::get("/firstreply/average_and_median/daily", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeDaily']);
Route::get("/firstreply/average_and_median/weekly", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeWeekly']);
Route::get("/firstreply/average_and_median/monthly", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeMonthly']);
Route::get("/resolutiontime/average_and_median/daily", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeDaily']);
Route::get("/resolutiontime/average_and_median/weekly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeWeekly']);
Route::get("/resolutiontime/average_and_median/monthly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeMonthly']);
Route::get("/creationtime/average/daily", [ReportsController::class, 'getAverageTicketCreationTimeDaily']);
Route::get("/resolutiontime/average_and_median/weekly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeWeekly']);
Route::get("/resolutiontime/average_and_median/monthly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeMonthly']);



// Route::group(["prefix", "reports"], function(){ 
    
//     //ticket reports
//     Route::group(['prefix', "ticket"], function(){

        

//     });
    
// });
// TicketRoutes::reportRoutes();
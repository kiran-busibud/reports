<?php

use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;
use App\Routes\ReportRoutes;
use App\Http\Controllers\EmailInfo\EmailInfoController;
use App\Http\Controllers\TenancyController;
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


// Route::get('/bychannels', [ReportsController::class, 'getUnresolvedTicketsByChannels']);
// Route::get('/bychannels/listview', [ReportsController::class, 'getUnresolvedTicketsByChannelsListview']);
// Route::get('/pendingtime', [ReportsController::class, 'getUnresolvedTicketsByPendingTime']);

// Route::get('/pendingtime/listview', [ReportsController::class, 'getUnresolvedTicketsByPendingTimeListview']);
// Route::get('/messages', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForth']);
// Route::get('/messages/listview', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForthListview']);
// Route::get('/tickets/notification', [ReportsController::class, 'getUnresolvedTicketsForNotification']);
// Route::get('/ticketIds/notification', [ReportsController::class, 'getUnresolvedTicketIdsForNotification']);
// Route::get('/tickets/replytime', [ReportsController::class, 'getTicketsByFirstReplyTimeBrackets']);
// Route::get("/tickets/resolutiontime", [ReportsController::class, 'getTicketsByResolutionTime']);

// Route::get("/tickets/creationtime/daily", [ReportsController::class, 'getTicketsByCreationTimeDaily']);
// Route::get("/tickets/creationtime/weekly", [ReportsController::class, 'getTicketsByCreationTimeWeekly']);
// Route::get("/tickets/creationtime/monthly", [ReportsController::class, 'getTicketsByCreationTimemonthly']);

// Route::get("/firstreply/average_and_median/daily", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeDaily']);
// Route::get("/firstreply/average_and_median/weekly", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeWeekly']);
// Route::get("/firstreply/average_and_median/monthly", [ReportsController::class, 'getAverageAndMedianOfFirstReplyTimeMonthly']);

// Route::get("/resolutiontime/average_and_median/daily", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeDaily']);
// Route::get("/resolutiontime/average_and_median/weekly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeWeekly']);
// Route::get("/resolutiontime/average_and_median/monthly", [ReportsController::class, 'getAverageAndMedianOfResolutionTimeMonthly']);

// Route::get("/creationtime/average/daily", [ReportsController::class, 'getAverageTicketCreationTimeDaily']);
// Route::get("/creationtime/average/weekly", [ReportsController::class, 'getAverageTicketCreationTimeWeekly']);
// Route::get("/creationtime/average/monthly", [ReportsController::class, 'getAverageTicketCreationTimeMonthly']);

// Route::get("/closed/daily", [ReportsController::class, 'getTicketsClosedByTimeDaily']);
// Route::get("/closed/weekly", [ReportsController::class, 'getTicketsClosedByTimeWeekly']);
// Route::get("/closed/monthly", [ReportsController::class, 'getTicketsClosedByTimeMonthly']);

// Route::get("/backlog/daily", [ReportsController::class, 'getBacklogTicketsDaily']);
// Route::get("/backlog/weekly", [ReportsController::class, 'getBacklogTicketsWeekly']);
// Route::get("/backlog/monthly", [ReportsController::class, 'getBacklogTicketsMonthly']);


Route::get("/emailnfo-post", [EmailInfoController::class, 'postEmailInfo']);
Route::get("/emailInfo-get", [EmailInfoController::class, 'getEmailInfo']);
Route::get("/emailInfo-getall", [EmailInfoController::class, 'getAllEmailInfo']);
Route::get("/emailInfo-delete", [EmailInfoController::class, 'deleteEmailInfo']);
Route::get("/emailInfo-update", [EmailInfoController::class, 'updateEmailInfo']);
Route::get("/test",function(Request $request){

})->middleware('api');

Route::prefix('reports')->group(function () {

    ReportRoutes::reportRoutes();
});

Route::post("/api/central/email/parse", [EmailInfoController::class, 'postEmailInfo']);

Route::get("/changeTenant", [TenancyController::class, 'changeTenancy']);
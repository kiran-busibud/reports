<?php

use App\Http\Controllers\TicketsController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;
// use routes\TicketRoutes;

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

Route::get('/unresolved-tickets-by-channels', [TicketsController::class, 'getUnresolvedTicketsByChannels']);
Route::get('/unresolved-tickets-by-channels-listview', [TicketsController::class, 'getUnresolvedTicketsByChannelsListView']);
Route::get('/unresolvedTicketsByPendingTime', [TicketsController::class, 'getUnresolvedTicketsByPendingTime']);
Route::get('/unresolvedTicketsByPendingTimeListview', [TicketsController::class, 'getUnresolvedTicketsByPendingTimeListView']);
Route::get('/unresolvedTicketByMostBackAndForth', [TicketsController::class, 'getUnresolvedTicketsByMostBackAndForth']);

Route::get('/bychannels', [ReportsController::class, 'getUnresolvedTicketsByChannels']);
Route::get('/bychannels/listview', [ReportsController::class, 'getUnresolvedTicketsByChannelsListview']);
Route::get('/pendingtime', [ReportsController::class, 'getUnresolvedTicketsByPendingTime']);
Route::get('/pendingtime/listview', [ReportsController::class, 'getUnresolvedTicketsByPendingTimeListview']);
Route::get('/messages', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForth']);
Route::get('/messages/listview', [ReportsController::class, 'getUnresolvedTicketsByMostBackAndForthListview']);


// Route::group(["prefix", "reports"], function(){ 
    
//     //ticket reports
//     Route::group(['prefix', "ticket"], function(){

//         routes/TicketRoutes::reportRoutes();

//     });

// });
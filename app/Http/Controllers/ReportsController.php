<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UnresolvedTicketRepository;

class ReportsController extends Controller
{
    protected $unresolvedTicketRepository;

    public function __construct(UnresolvedTicketRepository $unresolvedTicketRepository)
    {
        $this->unresolvedTicketRepository = $unresolvedTicketRepository;
    }

    function getUnresolvedTicketsByChannels(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByChannels();
        dd($tickets);
    }

    function getUnresolvedTicketsByChannelsListview(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByChannelsListView();
        dd($tickets);
    }

    function getUnresolvedTicketsByPendingTime(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByPendingTime();
        dd($tickets);
    }

    function getUnresolvedTicketsByPendingTimeListview(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByPendingTimeListview();
        dd($tickets);
    }

    function getUnresolvedTicketsByMostBackAndForth(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByMostBackAndForth();
        dd($tickets);
    }

    function getUnresolvedTicketsByMostBackAndForthListview(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByMostBackAndForthListview();
        dd($tickets);
    }

    function getUnresolvedTicketsForNotification(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsForNotification();
        dd($tickets);
    }

    function getUnresolvedTicketIdsForNotification(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketIdsForNotification();
        dd($tickets);
    }

    function getTicketsByFirstReplyTimeBrackets(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByFirstReplyTimeBrackets();
        dd($tickets);
    }

    function getTicketsByResolutionTime(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByResolutionTime();
        dd($tickets);
    }
    
    function getTicketsByCreationTimeDaily(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByCreationTimeDaily();
        dd($tickets);
    }

    function getTicketsByCreationTimeWeekly(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByCreationTimeWeekly();
        dd($tickets);
    }

    function getTicketsByCreationTimeMonthly(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByCreationTimeMonthly();
        dd($tickets);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TicketRepository;

class TicketsController extends Controller
{

    protected $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    function getUnresolvedTicketsByChannels(Request $request)
    {
        $tickets = $this->ticketRepository->getUnresolvedTicketsByChannels();
        dd($tickets);
    }

    function getUnresolvedTicketsByChannelsListView(Request $request)
    {
        $tickets = $this->ticketRepository->getUnresolvedTicketsByChannelsListView();
        dd($tickets);
    }

    function getUnresolvedTicketsByPendingTime(Request $request)
    {
        $tickets = $this->ticketRepository->getUnresolvedTicketsByPendingTime();
        dd($tickets);
    }

    function getUnresolvedTicketsByPendingTimeListView(Request $request)
    {
        $tickets = $this->ticketRepository->getUnresolvedTicketsByChannels();
        dd($tickets);
    }


    function getUnresolvedTicketsByMostBackAndForth(Request $request)
    {
        $tickets = $this->ticketRepository->getUnresolvedTicketsByMostBackAndForth();
        dd($tickets);
    }
}
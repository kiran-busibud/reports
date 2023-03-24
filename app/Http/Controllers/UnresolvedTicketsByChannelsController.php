<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TicketRepository;

class UnresolvedTicketsByChannelsController extends Controller
{
    
    protected $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    function getTickets(Request $request){
        $tickets = $this->ticketRepository->getAllTickets();
        dd($tickets);
    }
}

<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;


class TicketRepository
{

    function getAllTickets()
    {
        $data = DB::table('hl_ticket')
            ->get();
        return $data;
    }
}
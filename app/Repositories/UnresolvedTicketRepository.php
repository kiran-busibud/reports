<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;

class UnresolvedTicketRepository{
    function updateUnresolvedTickets(array $tickets, array $tag_mapping, array $message_counts_mapping){
        foreach ($tickets as $ticket) {
            DB::table('unresolved_tickets')->insert([
                'ticket_title' => $ticket->ticket_title,
                'ticket_description' => $ticket->ticket_description,
                'ticket_agent' => $ticket->ticket_agent,
                'ticket_status_id' => $ticket->ticket_status_id,
                'ticket_brand_id' => $ticket->ticket_brand_id,
                'ticket_channel' => $ticket->ticket_channel,
                'ticket_tags' => $tag_mapping[$ticket->id] ?? "",
                'ticket_total_messages' => $message_counts_mapping[$ticket->id]['total_messages'] ?? 0,
                'ticket_agent_messages' => $message_counts_mapping[$ticket->id]['agent_messages'] ?? 0,
                'ticket_customer_messages' => $message_counts_mapping[$ticket->id]['customer_messages'] ?? 0,
            ]);
        }
    }
}
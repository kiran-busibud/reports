<?php

namespace App\Repositories\ReportsService;

use Illuminate\Support\Facades\DB;

class ReportsService
{

    function cacheUnresolvedTickets()
    {
        $params = [];
        $unresolved_ticket_status_ids = [4, 6];
        $params = array_merge($params, $unresolved_ticket_status_ids);
        $query = "SELECT id,ticket_status_id,ticket_channel,ticket_agent,ticket_brand_id,ticket_title,ticket_description
                    FROM hl_ticket 
                    WHERE ticket_status_id in (" . implode(',', array_fill(0, count($unresolved_ticket_status_ids), '?')) . ")";

        $tickets = DB::select($query, $params);

        $params = [];
        $query = "SELECT ticket_id, meta_value
                    FROM hl_ticketmeta
                    WHERE meta_key = 'tags'";
        $tags = DB::select($query, $params);
        $tag_mapping = [];
        foreach ($tags as $tag) {
            $tag_mapping[$tag->ticket_id] = $tag->meta_value;
        }
        // dd(count($tags));

        $params = [];
        $query = "SELECT 
                    COUNT(hl_messages.message_id) AS total_messages,
                    COUNT(hl_messages.message_agent_id) AS agent_messages,
                    COUNT(hl_messages.message_customer_id) AS customer_messages,
                    message_ticket_id
                  FROM hl_messages
                  GROUP BY message_ticket_id";

        $ticket_message_count = DB::select($query, $params);


        $message_mapper = [];

        foreach ($ticket_message_count as $message_count) {
            $counts = [];
            $counts['total_messages'] = $message_count->total_messages;
            $counts['agent_messages'] = $message_count->agent_messages;
            $counts['customer_messages'] = $message_count->customer_messages;
            $message_mapper[$message_count->message_ticket_id] = $counts;
        }

        foreach ($tickets as $ticket) {
            DB::table('unresolved_tickets')->insert([
                'ticket_title' => $ticket->ticket_title,
                'ticket_description' => $ticket->ticket_description,
                'ticket_agent' => $ticket->ticket_agent,
                'ticket_status_id' => $ticket->ticket_status_id,
                'ticket_brand_id' => $ticket->ticket_brand_id,
                'ticket_channel' => $ticket->ticket_channel,
                'ticket_tags' => $tag_mapping[$ticket->id] ?? "",
                'ticket_total_messages' => $message_mapper[$ticket->id]['total_messages'] ?? 0,
                'ticket_agent_messages' => $message_mapper[$ticket->id]['agent_messages'] ?? 0,
                'ticket_customer_messages' => $message_mapper[$ticket->id]['customer_messages'] ?? 0,
            ]);
        }
    }

}
<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TicketMetaRepository
{
    function getTags()
    {
        $params = [];
        $query = "SELECT ticket_id, meta_value
                    FROM hl_ticketmeta
                    WHERE meta_key = 'tags'";
        $tags = DB::select($query, $params);
        return $tags;
    }

    function getMessages()
    {
        $params = [];
        $query = "SELECT 
                    COUNT(hl_messages.message_id) AS total_messages,
                    COUNT(hl_messages.message_agent_id) AS agent_messages,
                    COUNT(hl_messages.message_customer_id) AS customer_messages,
                    MIN(hl_messages.message_date) AS first_reply_time,
                    message_ticket_id
                  FROM hl_messages
                  WHERE message_agent_id IS NOT NULL
                  GROUP BY message_ticket_id";

        $message_counts = DB::select($query, $params);
        return $message_counts;
    }
}
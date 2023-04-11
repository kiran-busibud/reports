<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class LiveChatRepository
{

    protected $body = ['start_date' => '2023-03-04 00:00:00', 'end_date' => '2023-03-11 23:59:59'];

    function getMetaData()
    {
        $params = [];
        $query = "SELECT request_id, meta_key, meta_value
                    FROM hl_live_chat_requests_meta
                    WHERE meta_key = 'resolved'";

        $result = DB::select($query, $params);

        return $result;
    }

    function getLiveChatRequests()
    {
        $params = [];
        $query = "SELECT t1.id,t1.assigned_agent_id,t1.created_at,t1.general_status,
                    COUNT(t2.id) as message_count
                FROM hl_live_chat_requests as t1
                LEFT JOIN hl_live_chat_messages as t2 ON t1.id = t2.live_chat_request_id
                GROUP BY t1.id, t1.assigned_agent_id, t1.created_at, t1.general_status";

        $chats = DB::select($query, $params);
        return $chats;
    }

    function updateLiveChatsData(array $live_chats, array $meta_data)
    {
        foreach ($live_chats as $live_chat) {
            DB::table('live_chats_cache')->insert([
                'id' => $live_chat->id,
                'assigned_agent_id' => $live_chat->assigned_agent_id,
                'created_at' => $live_chat->created_at,
                'message_count' => $live_chat->message_count,
                'general_status' => $live_chat->general_status,
                'resolved' => $meta_data[$live_chat->id]['resolved'] ?? 0,
            ]);
        }
    }

    function getTotalChatsDaily()
    {
        $params = [];
        $query = "SELECT DATE(created_at) AS created_at,COUNT(ID) AS chat_count
        FROM live_chats_cache
        WHERE 1=1 ";

        if (isset($this->body['start_date']) && isset($this->body['end_date'])) {
            $start_date = date($this->body['start_date']);
            $end_date = date($this->body['end_date']);

            $query .= "AND created_at BETWEEN '$start_date' AND '$end_date'";
        }

        if (isset($this->body['chat_status'])) {
            $query .= "AND assigned = $this->body['chat_status']";
        }

        if (isset($this->body['assignment'])) {
            $query .= "AND assigned_agent_id IS NOT NULL";
        }

        $query .= " GROUP BY DATE(created_at)
                    ORDER BY DATE(created_at)";
        // dd($query);

        $result = DB::select($query, $params);
        return $result;
    }

    function getTotalChatsMonthly()
    {
        $params = [];
        $query = "SELECT DATE_FORMAT(created_at, '%M %Y') AS created_at,
                        COUNT(ID) AS chat_count 
                FROM live_chats_cache 
                WHERE 1=1 ";

        if (isset($this->body['start_date']) && isset($this->body['end_date'])) {
            $start_date = date($this->body['start_date']);
            $end_date = date($this->body['end_date']);

            $query .= "AND created_at BETWEEN '$start_date' AND '$end_date'";
        }

        if (isset($this->body['chat_status'])) {
            $query .= "AND assigned = $this->body['chat_status']";
        }

        if (isset($this->body['assignment'])) {
            $query .= "AND assigned_agent_id IS NOT NULL";
        }

        $query .= " GROUP BY DATE_FORMAT(created_at, '%M %Y')
                    ORDER BY MONTH(created_at)";
        // dd($query);

        $result = DB::select($query, $params);
        return $result;
    }

    function getChatsByTime()
    {
        $params = [];
        $query = "SELECT created_at 
                FROM live_chats_cache 
                WHERE 1=1 ";

        if (isset($this->body['start_date']) && isset($this->body['end_date'])) {
            $start_date = date($this->body['start_date']);
            $end_date = date($this->body['end_date']);

            $query .= "AND created_at BETWEEN '$start_date' AND '$end_date'";
        }

        if (isset($this->body['chat_status'])) {
            $query .= "AND assigned = $this->body['chat_status']";
        }

        if (isset($this->body['assignment'])) {
            $query .= "AND assigned_agent_id IS NOT NULL";
        }

        $query .= "ORDER BY created_at";
        // dd($query);

        $result = DB::select($query, $params);
        return $result;

    }
}
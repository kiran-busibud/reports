<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class LiveChatRepository
{


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
        $query = "SELECT t1.id,t1.assigned_agent_id,t1.created_at,
                    COUNT(t2.id) as message_count
                FROM hl_live_chat_requests as t1
                LEFT JOIN hl_live_chat_messages as t2 ON t1.id = t2.live_chat_request_id
                GROUP BY t1.id, t1.assigned_agent_id, t1.created_at";

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
                'resolved' => $meta_data[$live_chat->id]['resolved'] ?? 0,
            ]);
        }
    }
}
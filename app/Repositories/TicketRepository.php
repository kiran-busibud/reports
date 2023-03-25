<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;


class TicketRepository
{

    protected $body = ['ticket_status_id' => [6, 4]];
    function getUnresolvedTicketsByChannels()
    {

        // $query = DB::table('hl_ticket')
        //     ->select(DB::raw('COUNT(*) as count, ticket_channel, ticket_status_id'))
        //     ->whereIn('ticket_status_id',$this->body['ticket_status_id']);

        // if (in_array('ticket_status_id',$this->body)) {
        //     $ticketStatusId = $this->body['ticket_status_id'];
        //     if (is_array($ticketStatusId)) {
        //         $query->whereIn('ticket_status_id', $ticketStatusId);
        //     } else {
        //         $query->where('ticket_status_id', $ticketStatusId);
        //     }
        // }

        // if (in_array('ticket_channel',$this->body)) {
        //     $ticketChannels = $this->body['ticket_channels'];
        //     if (is_array($ticketChannels)) {
        //         $query->whereIn('ticket_channel', $ticketChannels);
        //     } else {
        //         $query->where('ticket_channel', $ticketChannels);
        //     }
        // }

        // if (in_array('ticket_brand_id',$this->body)) {
        //     $ticketBrandIds = $this->body['ticket_brand_id'];
        //     if (is_array($ticketChannels)) {
        //         $query->whereIn('ticket_brand_id', $ticketBrandIds);
        //     } else {
        //         $query->where('ticket_brand_id', $ticketBrandIds);
        //     }
        // }

        // if (in_array('ticket_agent', $this->body)) {
        //     $query->where('ticket_agent', $this->body['ticket_id']);
        // }

        // $query->groupBy(['ticket_channel', 'ticket_status_id']);
        // // dd($query->toSql());

        // $data = $query->get();

        // return $data;

        $query = "SELECT COUNT(*) as count,ticket_channel, ticket_status_id FROM hl_ticket WHERE 1 = 1";

        $params = [];

        if (isset($this->body['ticket_status_id'])) {
            $ticketStatusId = $this->body['ticket_status_id'];
            if (is_array($ticketStatusId)) {
                $query .= " AND ticket_status_id IN (" . implode(',', array_fill(0, count($ticketStatusId), '?')) . ")";
                $params = array_merge($params, $ticketStatusId);
            } else {
                $query .= " AND ticket_status_id = ?";
                $params[] = $ticketStatusId;
            }
        }

        if (isset($this->body['ticket_channel'])) {
            $ticketChannels = $this->body['ticket_channel'];
            if (is_array($ticketChannels)) {
                $query .= " AND ticket_channel IN (" . implode(',', array_fill(0, count($ticketChannels), '?')) . ")";
                $params = array_merge($params, $ticketChannels);
            } else {
                $query .= " AND ticket_channel = ?";
                $params[] = $ticketChannels;
            }
        }

        if (isset($this->body['ticket_brand_id'])) {
            $ticketBrandIds = $this->body['ticket_brand_id'];
            if (is_array($ticketBrandIds)) {
                $query .= " AND ticket_brand_id IN (" . implode(',', array_fill(0, count($ticketBrandIds), '?')) . ")";
                $params = array_merge($params, $ticketBrandIds);
            } else {
                $query .= " AND ticket_brand_id = ?";
                $params[] = $ticketBrandIds;
            }
        }

        if (isset($this->body['ticket_agent'])) {
            $query .= " AND ticket_agent = ?";
            $params[] = $this->body['ticket_agent'];
        }

        $query .= " GROUP BY ticket_channel, ticket_status_id";
        $result = DB::select($query, $params);
        return $result;
    }

    function getUnresolvedTicketsByPendingTime()
    {
        $params = [];
        $query = "SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 0 AND 4 THEN '0-4 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 5 AND 12 THEN '4-12 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 13 AND 24 THEN '12-24 hrs'
                ELSE '24 hrs+'
            END AS hrs_range, COUNT(*) as count, ticket_status_id FROM hl_ticket WHERE 1 = 1";

        if (isset($this->body['ticket_status_id'])) {
            $ticketStatusId = $this->body['ticket_status_id'];
            if (is_array($ticketStatusId)) {
                $query .= " AND ticket_status_id IN (" . implode(',', array_fill(0, count($ticketStatusId), '?')) . ")";
                $params = array_merge($params, $ticketStatusId);
            } else {
                $query .= " AND ticket_status_id = ?";
                $params[] = $ticketStatusId;
            }
        }

        if (isset($this->body['ticket_brand_id'])) {
            $ticketBrandIds = $this->body['ticket_brand_id'];
            if (is_array($ticketBrandIds)) {
                $query .= " AND ticket_brand_id IN (" . implode(',', array_fill(0, count($ticketBrandIds), '?')) . ")";
                $params = array_merge($params, $ticketBrandIds);
            } else {
                $query .= " AND ticket_brand_id = ?";
                $params[] = $ticketBrandIds;
            }
        }

        if (isset($this->body['ticket_agent'])) {
            $query .= " AND ticket_agent = ?";
            $params[] = $this->body['ticket_agent'];
        }

        $query .= " GROUP BY hrs_range, ticket_status_id";

        $result = DB::select($query, $params);
        return $result;
    }

    function getUnresolvedTicketsByMostBackAndForth(){
        $params = [];
        // $query = "SELECT COUNT(hl_messages.message_id) AS count_messages,
        // COUNT(hl_messages.message_agent_id) AS agent_messages,
        // COUNT(hl_messages.message_customer_id) AS customer_messages
        // FROM hl_ticket
        // LEFT JOIN hl_messages ON hl_ticket.id = hl_messages.message_ticket_id
        // GROUP BY hl_ticket.id";
        
        // $result = DB::select($query, $params);
        // return $result;

        $query = 
            "SELECT 
            CASE 
                WHEN count_messages BETWEEN 0 AND 2 THEN '0-2'
                WHEN count_messages BETWEEN 3 AND 8 THEN '3-8'
                ELSE '9+'
            END AS message_count_range, 
            COUNT(*) AS ticket_count,
            agent_messages, customer_messages
                FROM (
                    SELECT 
                    COUNT(hl_messages.message_id) AS count_messages,
                    COUNT(hl_messages.message_agent_id) AS agent_messages,
                    COUNT(hl_messages.message_customer_id) AS customer_messages
                FROM hl_ticket
                    LEFT JOIN hl_messages ON hl_ticket.id = hl_messages.message_ticket_id
                WHERE hl_ticket.some_column = some_value
                GROUP BY hl_ticket.id
                ) AS t
            GROUP BY message_count_range, agent_messages, customer_messages";
    }
}
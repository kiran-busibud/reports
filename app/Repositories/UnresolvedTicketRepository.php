<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UnresolvedTicketRepository
{
    protected $body = ['ticket_status_id' => [6, 4]];
    function updateUnresolvedTickets(array $tickets, array $tag_mapping, array $message_counts_mapping)
    {
        foreach ($tickets as $ticket) {
            DB::table('unresolved_tickets')->insert([
                'ticket_title' => $ticket->ticket_title,
                'ticket_description' => $ticket->ticket_description,
                'ticket_agent' => $ticket->ticket_agent,
                'ticket_status_id' => $ticket->ticket_status_id,
                'ticket_brand_id' => $ticket->ticket_brand_id,
                'ticket_channel' => $ticket->ticket_channel,
                'ticket_date' => $ticket->ticket_date,
                'ticket_tags' => $tag_mapping[$ticket->id] ?? "",
                'ticket_total_messages' => $message_counts_mapping[$ticket->id]['total_messages'] ?? 0,
                'ticket_agent_messages' => $message_counts_mapping[$ticket->id]['agent_messages'] ?? 0,
                'ticket_customer_messages' => $message_counts_mapping[$ticket->id]['customer_messages'] ?? 0,
            ]);
        }
    }

    function getUnresolvedTicketsByChannels()
    {
        $query = "SELECT COUNT(*) as count,ticket_channel, ticket_status_id FROM unresolved_tickets WHERE 1 = 1";

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

    function getUnresolvedTicketsByChannelsListview()
    {
        $query = "SELECT ticket_channel, ticket_status_id, ticket_title, ticket_description FROM unresolved_tickets WHERE 1 = 1";

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
            END AS hrs_range, COUNT(*) as count, ticket_status_id FROM unresolved_tickets WHERE 1 = 1";

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

    function getUnresolvedTicketsByPendingTimeListview()
    {
        $params = [];
        $query = "SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 0 AND 4 THEN '0-4 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 5 AND 12 THEN '4-12 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 13 AND 24 THEN '12-24 hrs'
                ELSE '24 hrs+'
            END AS hrs_range, ticket_title, ticket_description 
            FROM unresolved_tickets 
            WHERE 1 = 1";

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

        $result = DB::select($query, $params);
        return $result;
    }

    function getUnresolvedTicketsByMostBackAndForth()
    {
        $params = [];
        $query =
            "SELECT 
            CASE 
                WHEN ticket_total_messages BETWEEN 0 AND 2 THEN '0-2'
                WHEN ticket_total_messages BETWEEN 3 AND 4 THEN '3-4'
                WHEN ticket_total_messages BETWEEN 5 AND 8 THEN '5-8'
                ELSE '8+'
            END AS message_count_range, 
            SUM(ticket_agent_messages) AS ticket_agent_messages,
            SUM(ticket_customer_messages) AS ticket_customer_messages
            FROM unresolved_tickets
            WHERE 1 = 1";

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

        $query .= " GROUP BY message_count_range";

        $result = DB::select($query, $params);
        return $result;
    }

    function getUnresolvedTicketsByMostBackAndForthListview()
    {
        $params = [];
        $query =
            "SELECT 
            CASE 
                WHEN ticket_total_messages BETWEEN 0 AND 2 THEN '0-2'
                WHEN ticket_total_messages BETWEEN 3 AND 4 THEN '3-4'
                WHEN ticket_total_messages BETWEEN 5 AND 8 THEN '5-8'
                ELSE '8+'
            END AS message_count_range, ticket_title, ticket_description
            FROM unresolved_tickets
            WHERE 1 = 1";

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

        $result = DB::select($query, $params);
        return $result;
    }
}
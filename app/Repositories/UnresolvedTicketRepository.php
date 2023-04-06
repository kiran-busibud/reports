<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UnresolvedTicketRepository
{
    protected $body = ['ticket_status_id' => [6, 4], 'ticket_tags' => [8, 101]];
    function updateTickets(array $tickets, array $tag_mapping, array $message_mapping)
    {
        foreach ($tickets as $ticket) {
            DB::table('tickets_cache')->insert([
                'ticket_id' => $ticket->id,
                'ticket_title' => $ticket->ticket_title,
                'ticket_description' => $ticket->ticket_description,
                'ticket_agent' => $ticket->ticket_agent,
                'ticket_status_id' => $ticket->ticket_status_id,
                'ticket_brand_id' => $ticket->ticket_brand_id,
                'ticket_channel' => $ticket->ticket_channel,
                'ticket_date' => $ticket->ticket_date,
                'ticket_closed_date' => $ticket->ticket_closed_date ?? NULL,
                'ticket_tags' => $tag_mapping[$ticket->id] ?? "",
                'ticket_total_messages' => $message_mapping[$ticket->id]['total_messages'] ?? 0,
                'ticket_agent_messages' => $message_mapping[$ticket->id]['agent_messages'] ?? 0,
                'ticket_customer_messages' => $message_mapping[$ticket->id]['customer_messages'] ?? 0,
                'ticket_first_reply_time' => $message_mapping[$ticket->id]['first_reply_time'],
            ]);
        }
    }

    function getUnresolvedTicketsByChannels()
    {
        $query = "SELECT COUNT(*) as count,ticket_channel, ticket_status_id FROM tickets_cache WHERE 1 = 1";

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

        if (isset($this->body['ticket_tags'])) {
            $tags = $this->body['ticket_tags'];
            $regex = '(' . implode('|', $tags) . ')';
            $regex = ',' . $regex . ',';
            $regex = "'" . $regex . "'";
            $query .= " AND CONCAT(',', ticket_tags  , ',') REGEXP $regex";
        }

        $query .= " GROUP BY ticket_channel, ticket_status_id";
        // dd($query);
        // dd($params);
        $result = DB::select($query, $params);
        return $result;
    }

    function getUnresolvedTicketsByChannelsListview()
    {
        $query = "SELECT ticket_channel, ticket_status_id, ticket_title, ticket_description FROM tickets_cache WHERE 1 = 1";

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
            END AS hrs_range, COUNT(*) as count, ticket_status_id FROM tickets_cache WHERE 1 = 1";

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
            FROM tickets_cache 
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
            FROM tickets_cache
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
            FROM tickets_cache
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

    function getUnresolvedTicketsForNotification()
    {
        $count = 10;
        $time_in_days = 1000;
        $query = "SELECT ticket_title, ticket_description 
                    FROM tickets_cache 
                    WHERE TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 0 AND $time_in_days";

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

        $query .= "LIMIT $count";

        $result = DB::select($query, $params);
        return $result;
    }

    function getUnresolvedTicketIdsForNotification()
    {
        $count = 5;
        $time_in_days = 1000;
        $query = "SELECT id 
                    FROM tickets_cache 
                    WHERE TIMESTAMPDIFF(HOUR, ticket_date, CURRENT_DATE()) BETWEEN 0 AND $time_in_days";

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

        $query .= " ORDER BY ticket_date DESC";
        $query .= " LIMIT $count";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketsByFirstReplyTimeBrackets()
    {
        $params = [];
        $query = "SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, ticket_first_reply_time) BETWEEN 0 AND 4 THEN '0-4 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, ticket_first_reply_time) BETWEEN 5 AND 12 THEN '4-12 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, ticket_first_reply_time) BETWEEN 13 AND 24 THEN '12-24 hrs'
                ELSE '24 hrs+'
            END AS hrs_range, COUNT(*) as count 
            FROM tickets_cache WHERE 1 = 1";

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

        $query .= " GROUP BY hrs_range";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketsByResolutionTime()
    {
        $params = [];
        $query = "SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, ticket_closed_date) BETWEEN 0 AND 4 THEN '0-4 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, ticket_closed_date) BETWEEN 5 AND 12 THEN '4-12 hrs'
                WHEN TIMESTAMPDIFF(HOUR, ticket_date, ticket_closed_date) BETWEEN 13 AND 24 THEN '12-24 hrs'
                ELSE '24 hrs+'
            END AS hrs_range, COUNT(*) as count 
            FROM tickets_cache 
            WHERE ticket_closed_date IS NOT NULL";

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

        $query .= " GROUP BY hrs_range";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketsByCreationTimeDaily()
    {
        $query = "SELECT COUNT(*) as count, DATE(ticket_date) AS day, ticket_status_id 
                    FROM tickets_cache 
                    WHERE 1 = 1";

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

        $query .= " GROUP BY ticket_status_id, day";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketsByCreationTimeWeekly()
    {
        $params = [];
        $query = "SELECT 
                    CONCAT(DATE_FORMAT(MIN(ticket_date), '%b %e %y'), ' - ', DATE_FORMAT(MAX(ticket_date), '%b %e %y')) AS week_range,
                    COUNT(*) AS total_count, ticket_status_id
                FROM tickets_cache
                GROUP BY ticket_status_id, YEARWEEK(ticket_date)
                ORDER BY MIN(ticket_date)";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketsByCreationTimeMonthly()
    {
        $params = [];
        $query = "SELECT 
                    DATE_FORMAT(MAX(ticket_date), '%b %y') AS month,
                    COUNT(*) AS total_count, ticket_status_id
                FROM tickets_cache
                GROUP BY ticket_status_id, MONTH(ticket_date)
                ORDER BY MIN(ticket_date)";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketReplyTimesByDate($days)
    {
        $params = [];
        $query = "SELECT ticket_date, TIMESTAMPDIFF(MINUTE, ticket_date, ticket_first_reply_time) as reply_time
                FROM tickets_cache
                WHERE ticket_date >= DATE_SUB(NOW(), INTERVAL $days DAY)";

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

        if (isset($this->body['ticket_tags'])) {
            $tags = $this->body['ticket_tags'];
            $regex = '(' . implode('|', $tags) . ')';
            $regex = ',' . $regex . ',';
            $regex = "'" . $regex . "'";
            $query .= " AND CONCAT(',', ticket_tags  , ',') REGEXP $regex";
        }

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketReplyTimesByDateForMonths($months)
    {
        $params = [];
        $query = "SELECT ticket_date, TIMESTAMPDIFF(MINUTE, ticket_date, ticket_first_reply_time) as reply_time
                FROM tickets_cache
                WHERE ticket_date >= DATE_SUB(NOW(), INTERVAL $months MONTH)";

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

        if (isset($this->body['ticket_tags'])) {
            $tags = $this->body['ticket_tags'];
            $regex = '(' . implode('|', $tags) . ')';
            $regex = ',' . $regex . ',';
            $regex = "'" . $regex . "'";
            $query .= " AND CONCAT(',', ticket_tags  , ',') REGEXP $regex";
        }

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketResolutionTimesByDate($days)
    {
        $params = [];
        $query = "SELECT ticket_date, TIMESTAMPDIFF(MINUTE, ticket_date, ticket_closed_date) as resolution_time
                FROM tickets_cache
                WHERE ticket_date >= DATE_SUB(NOW(), INTERVAL $days DAY)";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketResolutionTimesByDateForMonths($months)
    {
        $params = [];
        $query = "SELECT ticket_date, TIMESTAMPDIFF(MINUTE, ticket_date, ticket_closed_date) as resolution_time
                FROM tickets_cache
                WHERE ticket_date >= DATE_SUB(NOW(), INTERVAL $months MONTH)";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketsWithRespondedToAndClosedWithoutResponseByDay($days)
    {
        $params = [];
        $query = "SELECT DATE(ticket_date) as ticket_date,
                    SUM(CASE WHEN ticket_agent_messages > 0 THEN 1 ELSE 0 END) AS responded_to,
                    SUM(CASE WHEN ticket_closed_date IS NOT NULL AND ticket_agent_messages = 0 THEN 1 ELSE 0 END) as closed_without_response
                FROM tickets_cache
                WHERE ticket_date >= DATE_SUB(NOW(), INTERVAL $days DAY)";

        $query .= "GROUP BY DATE(ticket_date);";

        $result = DB::select($query, $params);
        return $result;
    }

    function getTicketsWithRespondedToAndClosedWithoutResponseByMonth($months)
    {
        $params = [];
        $query = "SELECT DATE(ticket_date) as ticket_date,
                    SUM(CASE WHEN ticket_agent_messages > 0 THEN 1 ELSE 0 END) AS responded_to,
                    SUM(CASE WHEN ticket_closed_date IS NOT NULL AND ticket_agent_messages = 0 THEN 1 ELSE 0 END) as closed_without_response
                FROM tickets_cache
                WHERE ticket_date >= DATE_SUB(NOW(), INTERVAL $months MONTH)";

        $query .= "GROUP BY DATE(ticket_date);";

        $result = DB::select($query, $params);
        return $result;
    }
}
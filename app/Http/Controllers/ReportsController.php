<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UnresolvedTicketRepository;
use DateTime;
use DateTimeZone;

class ReportsController extends Controller
{
    protected $unresolvedTicketRepository;

    public function __construct(UnresolvedTicketRepository $unresolvedTicketRepository)
    {
        $this->unresolvedTicketRepository = $unresolvedTicketRepository;
    }

    public function getMedian(array $data)
    {
        $count = count($data);

        if ($count == 0)
            return 0;

        sort($data, SORT_NUMERIC);

        $mid = floor(($count - 1) / 2);

        if ($count % 2) {
            $median = $data[$mid];
        } else {
            $low = $data[$mid];
            $high = $data[$mid + 1];
            $median = (($low + $high) / 2);
        }

        return $median;
    }

    public function getAverage(array $data)
    {
        $count = count($data);
        $sum = 0;

        if ($count == 0)
            return 0;

        foreach ($data as $e) {
            $sum += $e;
        }

        return $sum / $count;
    }

    public function getWeeks($num_days)
    {
        // Get the current date and time in the user's timezone
        $now = new DateTime('now', new DateTimeZone('UTC'));

        // Calculate the start date and end date of the week that contains the current date
        $start = clone $now;
        $start->modify('monday this week');
        $end = clone $start;
        $end->modify("+6 days");

        // Calculate the start date of the first week
        $first_week_start = clone $start;
        $first_week_start->modify("-" . ($num_days - 7) . " days");

        // Build an array of week ranges
        $week_ranges = array();
        $week_start = clone $first_week_start;
        while ($end >= $now && $week_start < $end) {
            $week_end = clone $week_start;
            $week_end->modify("+6 days");
            $week_ranges[] = $week_start->format('Mj') . "-" . $week_end->format('Mj');
            $week_start->modify("+7 days");
        }

        // If the last week ends after the current date, adjust the last week range to end on the current date
        if ($week_ranges) {
            $last_week_range = end($week_ranges);
            $last_week_end = DateTime::createFromFormat('Mj', substr($last_week_range, 4));
            if ($last_week_end > $now) {
                $last_week_range = substr($last_week_range, 0, 4) . $now->format('j');
                $week_ranges[count($week_ranges) - 1] = $last_week_range;
            }
        }

        return $week_ranges;
    }
    function getUnresolvedTicketsByChannels(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByChannels();
        dd($tickets);
    }

    function getUnresolvedTicketsByChannelsListview(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByChannelsListView();
        dd($tickets);
    }

    function getUnresolvedTicketsByPendingTime(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByPendingTime();
        dd($tickets);
    }

    function getUnresolvedTicketsByPendingTimeListview(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByPendingTimeListview();
        dd($tickets);
    }

    function getUnresolvedTicketsByMostBackAndForth(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByMostBackAndForth();
        dd($tickets);
    }

    function getUnresolvedTicketsByMostBackAndForthListview(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsByMostBackAndForthListview();
        dd($tickets);
    }

    function getUnresolvedTicketsForNotification(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketsForNotification();
        dd($tickets);
    }

    function getUnresolvedTicketIdsForNotification(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getUnresolvedTicketIdsForNotification();
        dd($tickets);
    }

    function getTicketsByFirstReplyTimeBrackets(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByFirstReplyTimeBrackets();
        dd($tickets);
    }

    function getTicketsByResolutionTime(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByResolutionTime();
        dd($tickets);
    }

    function getTicketsByCreationTimeDaily(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByCreationTimeDaily();
        dd($tickets);
    }

    function getTicketsByCreationTimeWeekly(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByCreationTimeWeekly();
        dd($tickets);
    }

    function getTicketsByCreationTimeMonthly(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketsByCreationTimeMonthly();
        dd($tickets);
    }

    function getAverageAndMedianOfFirstReplyTimeDaily(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketReplyTimesByDate(150);
        // dd(strtotime($tickets[0]->ticket_date));
        $tickets_by_days = [];
        foreach ($tickets as $ticket) {
            $timestamp = strtotime($ticket->ticket_date);
            $date = date('M', $timestamp) . date('d', $timestamp);
            $tickets_by_days[$date][] = $ticket->reply_time;
        }
        // dd($tickets_by_days);
        $result = [];
        foreach ($tickets_by_days as $key => $values) {
            $result[$key]['average'] = $this->getAverage($values);
            $result[$key]['median'] = $this->getMedian($values);
        }
        dd($result);
    }

    function getAverageAndMedianOfFirstReplyTimeWeekly(Request $request)
    {
        dd($this->getWeeks(150));
        // $tickets = $this->unresolvedTicketRepository->getTicketReplyTimesByDate(150);
        // // dd(strtotime($tickets[0]->ticket_date));
        // $tickets_by_weeks = [];
        // $weeks = $this->getWeeks(150);
        // foreach ($tickets as $ticket) {
        //     $timestamp = strtotime($ticket->ticket_date);
        //     $date = date('M', $timestamp) . date('d', $timestamp);
        //     $tickets_by_days[$date][] = $ticket->reply_time;
        // }
        // // dd($tickets_by_days);
        // $result = [];
        // foreach ($tickets_by_days as $key => $values) {
        //     $result[$key]['average'] = $this->getAverage($values);
        //     $result[$key]['median'] = $this->getMedian($values);
        // }
        // dd($result);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UnresolvedTicketRepository;
use DateTime;
use DateInterval;

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

    public function getDaysToWeeksMapping($weeks)
    {
        $cur_time = time();
        $cur_date = new DateTime("@$cur_time");
        $mapping = [];

        for ($week = 0; $week < $weeks; $week++) {
            $cur_week_end_date = clone $cur_date;
            $cur_week_start_date = clone $cur_date;
            $cur_week_start_date->sub(new DateInterval('P6D'));
            $value = $cur_week_start_date->format('Mj') . "-" . $cur_week_end_date->format('M j');
            for ($day = 0; $day < 7; $day++) {
                $mapping[$cur_date->format('Mj')] = $value;
                $cur_date->sub(new DateInterval('P1D'));
            }
        }
        // dd($mapping);
        return $mapping;
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
        $days_to_week_mapping = $this->getDaysToWeeksMapping(21);
        $tickets = $this->unresolvedTicketRepository->getTicketReplyTimesByDate(147);

        $tickets_by_weeks = [];
        foreach ($tickets as $ticket) {
            $timestamp = strtotime($ticket->ticket_date);
            $date = date('M', $timestamp) . date('j', $timestamp);
            $tickets_by_weeks[$days_to_week_mapping[$date]][] = $ticket->reply_time;
        }
        // dd($tickets_by_days);
        $result = [];
        foreach ($tickets_by_weeks as $key => $values) {
            $result[$key]['average'] = $this->getAverage($values);
            $result[$key]['median'] = $this->getMedian($values);
        }
        dd($result);
    }

    function getAverageAndMedianOfFirstReplyTimeMonthly(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketReplyTimesByDateForMonths(12);

        $tickets_by_months = [];
        foreach ($tickets as $ticket) {
            $timestamp = strtotime($ticket->ticket_date);
            $month = date('M', $timestamp);
            $tickets_by_months[$month][] = $ticket->reply_time;
        }

        $result = [];
        foreach ($tickets_by_months as $key => $values) {
            $result[$key]['average'] = $this->getAverage($values);
            $result[$key]['median'] = $this->getMedian($values);
        }
        dd($result);
    }

    function getAverageAndMedianOfResolutionTimeDaily(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketResolutionTimesByDate(150);
        // dd(strtotime($tickets[0]->ticket_date));
        $tickets_by_days = [];
        foreach ($tickets as $ticket) {
            $timestamp = strtotime($ticket->ticket_date);
            $date = date('M', $timestamp) . date('d', $timestamp);
            $tickets_by_days[$date][] = $ticket->resolution_time;
        }
        // dd($tickets_by_days);
        $result = [];
        foreach ($tickets_by_days as $key => $values) {
            $result[$key]['average'] = $this->getAverage($values);
            $result[$key]['median'] = $this->getMedian($values);
        }
        dd($result);
    }

    function getAverageAndMedianOfResolutionTimeWeekly(Request $request)
    {
        $days_to_week_mapping = $this->getDaysToWeeksMapping(21);
        $tickets = $this->unresolvedTicketRepository->getTicketResolutionTimesByDate(147);

        $tickets_by_weeks = [];
        foreach ($tickets as $ticket) {
            $timestamp = strtotime($ticket->ticket_date);
            $date = date('M', $timestamp) . date('j', $timestamp);
            $tickets_by_weeks[$days_to_week_mapping[$date]][] = $ticket->resolution_time;
        }
        // dd($tickets_by_days);
        $result = [];
        foreach ($tickets_by_weeks as $key => $values) {
            $result[$key]['average'] = $this->getAverage($values);
            $result[$key]['median'] = $this->getMedian($values);
        }
        dd($result);
    }

    function getAverageAndMedianOfResolutionTimeMonthly(Request $request)
    {
        $tickets = $this->unresolvedTicketRepository->getTicketResolutionTimesByDateForMonths(12);

        $tickets_by_months = [];
        foreach ($tickets as $ticket) {
            $timestamp = strtotime($ticket->ticket_date);
            $month = date('M', $timestamp);
            $tickets_by_months[$month][] = $ticket->resolution_time;
        }

        $result = [];
        foreach ($tickets_by_months as $key => $values) {
            $result[$key]['average'] = $this->getAverage($values);
            $result[$key]['median'] = $this->getMedian($values);
        }
        dd($result);
    }

    function getAverageTicketCreationTimeDaily()
    {
        $average_tickets_per_day = $this->unresolvedTicketRepository->getTicketsWithRespondedToAndClosedWithoutResponseByDay(30);
        dd($average_tickets_per_day);
    }

    function getAverageTicketCreationTimeWeekly()
    {

        $days_to_week_mapping = $this->getDaysToWeeksMapping(21);
        $average_tickets_per_day = $this->unresolvedTicketRepository->getTicketsWithRespondedToAndClosedWithoutResponseByDay(147);

        $average_tickets_per_week
        foreach($)
        dd($tickets);
    }
}
<?php

namespace App\Services;

use App\Repositories\TicketRepository;
use App\Repositories\TicketMetaRepository;
use App\Repositories\UnresolvedTicketRepository;

class ReportsService
{

    protected $ticketRepository;
    protected $ticketMetaRepository;

    protected $unresolvedTicketRepository;
    public function __construct(TicketRepository $ticketRepository, TicketMetaRepository $ticketMetaRepository, UnresolvedTicketRepository $unresolvedTicketRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->ticketMetaRepository = $ticketMetaRepository;
        $this->unresolvedTicketRepository = $unresolvedTicketRepository;
    }

    function getTagMapper(array $tags)
    {
        $tag_mapping = [];
        foreach ($tags as $tag) {
            $tag_mapping[$tag->ticket_id] = $tag->meta_value;
        }
        return $tag_mapping;
    }

    function getMessageCountsMapper(array $message_counts)
    {
        $message_counts_mapping = [];

        foreach ($message_counts as $message_count) {
            $counts = [];
            $counts['total_messages'] = $message_count->total_messages;
            $counts['agent_messages'] = $message_count->agent_messages;
            $counts['customer_messages'] = $message_count->customer_messages;
            $message_counts_mapping[$message_count->message_ticket_id] = $counts;
        }
        return $message_counts_mapping;
    }

    function cacheUnresolvedTickets()
    {

        $tickets = $this->ticketRepository->getUnresolvedTickets();

        $tags = $this->ticketMetaRepository->getTags();

        $message_counts = $this->ticketMetaRepository->getMessageCounts();

        $tag_mapping = $this->getTagMapper($tags);

        $message_counts_mapping = $this->getMessageCountsMapper($message_counts);

        $this->unresolvedTicketRepository->updateUnresolvedTickets($tickets, $tag_mapping, $message_counts_mapping);

    }
}
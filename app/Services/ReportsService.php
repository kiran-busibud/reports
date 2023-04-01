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

    function getMessageMapper(array $messages)
    {
        $message_mapping = [];

        foreach ($messages as $message) {
            $data = [];
            $data['total_messages'] = $message->total_messages;
            $data['agent_messages'] = $message->agent_messages;
            $data['customer_messages'] = $message->customer_messages;
            $data['first_reply_time'] = $message->first_reply_time;
            
            $message_mapping[$message->message_ticket_id] = $data;
        }
        return $message_mapping;
    }

    function cacheTickets()
    {

        $tickets = $this->ticketRepository->getTickets();

        $tags = $this->ticketMetaRepository->getTags();

        $messages = $this->ticketMetaRepository->getMessages();

        $tag_mapping = $this->getTagMapper($tags);

        $message_mapping = $this->getMessageMapper($messages);

        $this->unresolvedTicketRepository->updateTickets($tickets, $tag_mapping, $message_mapping);

    }
}
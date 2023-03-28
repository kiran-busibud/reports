<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;

use Illuminate\Console\Command;

class PopulateTicketReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:populate-ticket-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'precache the unresolved tickets';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $params = [];
        $unresolved_ticket_status_ids = [4,6];
        $params = array_merge($params, $unresolved_ticket_status_ids);
        $query = "SELECT id,ticket_status_id,ticket_channel,ticket_agent,ticket_brand_id,ticket_title,ticket_description
                    FROM hl_ticket 
                    WHERE ticket_status_id in (" . implode(',', array_fill(0, count($unresolved_ticket_status_ids), '?')) . ")";
        
        // $tickets = [];
        // $unresolved_ticket_status_ids = [4,6];
        // $$tickets = DB::table('hl_ticket')
        //             ->select('id','ticket_status_id','ticket_channel','ticket_agent','ticket_brand_id','ticket_title','ticket_description')
        //             ->whereIn('ticket_status_id', $unresolved_ticket_status_ids)
        //             ->get();
        $tickets = DB::select($query,$params);
        // dd($tickets);

        $params = [];
        $query = "SELECT ticket_id, meta_value
                    FROM hl_ticketmeta
                    WHERE meta_key = 'tags'";
        $tags = DB::select($query,$params);
        $tag_mapping = [];
        foreach($tags as $tag){
            $tag_mapping[$tag->ticket_id] = $tag->meta_value;
        }
        // dd(count($tags));

        foreach($tickets as $ticket){
            DB::table('ticket_reports')->insert([
                'ticket_title' => $ticket->ticket_title,
                'ticket_description' => $ticket->ticket_description,
                'ticket_agent' => $ticket->ticket_agent,
                'ticket_status_id' => $ticket->ticket_status_id,
                'ticket_brand_id' => $ticket->ticket_brand_id,
                'ticket_channel' => $ticket->ticket_channel,
                'ticket_tags' => $tag_mapping[$ticket->id] ?? ""
            ]);
        }
        // dd(gettype($tickets[0]->id));
    }
}

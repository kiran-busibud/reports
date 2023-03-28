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
        $tickets = [];
        $unresolved_ticket_status_ids = [4,6];
        $$tickets = DB::table('hl_ticket')
                    ->select('id','ticket_status_id','ticket_channel','ticket_agent','ticket_brand_id','ticket_title','ticket_description')
                    ->whereIn('ticket_status_id', $unresolved_ticket_status_ids)
                    ->get();
        dd($tickets);
    }
}

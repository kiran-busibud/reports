<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Services\ReportsService;

class CacheTicketReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:cache-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'precache tickets along with metadata required for ticket reports';

    /**
     * Execute the console command.
     */
    protected $reportsService;
    public function __construct(ReportsService $reportsService)
    {
        parent::__construct();

        $this->reportsService = $reportsService;
    }
    
    public function handle(): void
    {

        $this->reportsService->cacheTickets();

    }
}
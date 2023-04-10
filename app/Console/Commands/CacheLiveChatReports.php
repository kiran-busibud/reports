<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReportsService;

class CacheLiveChatReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:cache-live-chat-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'precache live chats along with the meta data required for live chat reports';

    protected $reportsService;
    public function __construct(ReportsService $reportsService)
    {
        parent::__construct();

        $this->reportsService = $reportsService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->reportsService->cacheLiveChats();   
    }
}

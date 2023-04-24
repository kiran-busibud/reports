<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailInfo\EmailInfoService;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send emails';

    /**
     * Execute the console command.
     */

     protected $emailInfoService;
    public function __construct(EmailInfoService $emailInfoService)
    {
        parent::__construct();
        $this->emailInfoService = $emailInfoService;
    }
    public function handle(): void
    {
        $emails = $this->emailInfoService->getAllEmails();
        dd($emails);
    }
}

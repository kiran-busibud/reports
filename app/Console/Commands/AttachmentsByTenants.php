<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailInfo\EmailInfoService;
use App\Services\EmailInfo\AttachmentService;
use App\Services\EmailInfo\EmailParseService;
class AttachmentsByTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:attachments-by-tenants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

     protected $emailInfoService;
     protected $attachmentService;
     protected $emailParseService;

     public function __construct(EmailInfoService $emailInfoService, AttachmentService $attachmentService, EmailParseService $emailParseService){
        
        parent::__construct();

        $this->emailInfoService = $emailInfoService;
        $this->attachmentService = $attachmentService;
        $this->emailParseService = $emailParseService;
     }
    
    public function handle(): void
    {
        $this->emailParseService->parseEmailData();
    }
}

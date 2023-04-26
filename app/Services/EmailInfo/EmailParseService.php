<?php

namespace App\Services\EmailInfo;

use App\Services\EmailInfo\EmailInfoService;
class EmailParseService
{
    protected $emailInfoService;
    protected $attachmentService;

    function __construct(EmailInfoService $emailInfoService, AttachmentService $attachmentService)
    {
    $this->emailInfoService = $emailInfoService;
        $this->attachmentService = $attachmentService;
    }
    function parseEmailData()
    {

        $attachments = $this->emailInfoService->getAttachmentsWithTenant();

        $this->attachmentService->addAttachmentsToTenantDirectory($attachments);
    }
}
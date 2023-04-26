<?php

namespace App\Services\EmailInfo;

use App\Repositories\EmailInfo\EmailInfoRepository;
use App\Repositories\EmailInfo\AttachmentRepository;

class EmailInfoService
{
    protected $emailInfoRepository;
    protected $attachmentRepository;
    function __construct(EmailInfoRepository $emailInfoRepository, AttachmentRepository $attachmentRepository){
        $this->emailInfoRepository = $emailInfoRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    function getTenantFromEmail(string $email)
    {
        $tenant = random_int(1,10);
        return $tenant;
    }

    function postEmailInfo($payload)
    {
        $envelope = $payload['payload']['envelope'];
        $toEmails = $envelope['to'];
        $payload['tenant'] = $this->getTenantFromEmail($toEmails[count($toEmails)-1]);
        $payload['payload'] = json_encode($payload['payload']);

        return $this->emailInfoRepository->create($payload);
    }

    function getAllEmails()
    {
        $emails = $this->emailInfoRepository->getAll();
        return $emails;
    }

    function getAttachmentsWithTenant()
    {
        $attachments = $this->emailInfoRepository->getAttachmentsWithTenant();

        return $attachments;
    }
    
}
<?php

namespace App\Services\EmailInfo;

use App\Repositories\EmailInfo\emailInfoRepository;
use App\Repositories\EmailInfo\AttachmentRepository;

class EmailInfoService
{
    protected $emailInfoRepository;
    protected $attachmentRepository;
    function __construct(emailInfoRepository $emailInfoRepository, AttachmentRepository $attachmentRepository){
        $this->emailInfoRepository = $emailInfoRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    function getTenantFromEmail(string $email)
    {
        $tenant = random_int(1,2);
        if($tenant == 2){
            $tenant = null;
        }
        if($email == 'example3@example.com')
        {
            return 222;
        }
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
}
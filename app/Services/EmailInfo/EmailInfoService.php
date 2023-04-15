<?php

namespace App\Services\EmailInfo;

use App\Repositories\EmailInfo\emailInfoRepository;

class EmailInfoService
{
    protected $emailInfoRepository;
    function __construct(emailInfoRepository $emailInfoRepository){
        $this->emailInfoRepository = $emailInfoRepository;
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
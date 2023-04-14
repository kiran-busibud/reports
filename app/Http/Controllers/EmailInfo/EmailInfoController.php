<?php

namespace App\Http\Controllers\EmailInfo;

use App\Repositories\EmailInfo\IEmailInfoRepository;
use App\Repositories\EmailInfo\EmailInfoRepository;

class EmailInfoController
{
    protected $emailInfoRepository;

    protected $payload = [
        'id'=>3,
        'payload'=>'payload',
        'is_processed' => false,
        'fail_count'=>1,
        'created_at'=>'2023-04-14',
        'updated_at'=>NULL,
        'is_deleted'=>0,
    ];

    protected $payload1 = [
        'id'=>1,
        'payload'=>'payloadnew',
        'is_processed' => false,
        'fail_count'=>1,
        'created_at'=>'2023-04-14',
        'updated_at'=>NULL,
        'is_deleted'=>0,
    ];
    public function __construct(IEmailInfoRepository $emailInfoRepository)
    {
        $this->emailInfoRepository = $emailInfoRepository;
    }

    public function postEmailInfo()
    {
        $result = $this->emailInfoRepository->create($this->payload);
        dd($result);
    }

    public function getEmailInfo()
    {
        $result = $this->emailInfoRepository->getById(2);
        dd($result);
    }

    public function getAllEmailInfo()
    {
        $result = $this->emailInfoRepository->getAll();
        dd($result);
    }

    public function deleteEmailInfo()
    {
        $result = $this->emailInfoRepository->delete(3);
        dd($result);
    }

    public function updateEmailInfo()
    {
        $result = $this->emailInfoRepository->update(1, $this->payload1);
        dd($result);
    }
}

<?php

namespace App\Http\Controllers\EmailInfo;

use App\Repositories\EmailInfo\IEmailInfoRepository;
use App\Services\EmailInfo\EmailInfoService;

class EmailInfoController
{
    protected $emailInfoRepository;
    protected $emailInfoService;

    protected $payload = [
        'id' => 10,
        'payload' => [
            "envelope" => [
                "to" => [
                    "example@example.com",
                    "example2@example.com",
                    "example3@example.com",
                ],
                "from" => [
                    "sender@example.com"
                ]
            ],
            "headers" => [
                "Received" => [
                    "by mx0047p1mdw1.sendgrid.net with SMTP id 6WCVv7KAWn Wed, 27 Jul 2016 20:53:06 +0000 (UTC)",
                    "from mail-io0-f169.google.com (mail-io0-f169.google.com [209.85.223.169]) by mx0047p1mdw1.sendgrid.net (Postfix) with ESMTPS id AA9FFA817F2 for <example@example.comom>; Wed, 27 Jul 2016 20:53:06 +0000 (UTC)",
                    "by mail-io0-f169.google.com with SMTP id b62so81593819iod.3 for <example@example.comom>; Wed, 27 Jul 2016 13:53:06 -0700 (PDT)"
                ],
                "DKIM-Signature" => "v=1; a=rsa-sha256; c=relaxed/relaxed; d=sendgrid.com; s=ga1; h=mime-version:from:date:message-id:subject:to; bh=DpB1CYYeumytcPF3q0Upvx3Sq/oF4ZblEwnuVzFwqGI=; b=GH5YTxjt6r4HoTa+94w6ZGQszFQSgegF+Jlv69YV76OLycJI4Gxdwfh6Wlqfez5yID 5dsWuqaVJZQyMq/Dy/c2gHSqVo60BKG56YrynYeSrMPy8abE/6/muPilYxDoPoEyIr/c UXH5rhOKjmJ7nICKu1o99Tfl0cXyCskE7ERW0=",
                "X-Google-DKIM-Signature" => "v=1; a=rsa-sha256; c=relaxed/relaxed; d=1e100.net; s=20130820; h=x-gm-message-state:mime-version:from:date:message-id:subject:to; bh=DpB1CYYeumytcPF3q0Upvx3Sq/oF4ZblEwnuVzFwqGI=; b=Sq6LVHbmywBdt3sTBn19U8VOmelfoJltz8IcnvcETZsYwk96RBxN+RKMN5fOZSKw4j 15HrgdIFfyDmp67YK0ygvOITlTvZ6XY5I0PtnvDtAQt79kS3tKjI3QKJoEp/ZjIjSzlL KG7agl6cxFgBbIN0yHWBOvy3O+ZXY8tZdom1yOvULjmjW1U9JkdOs+aJ6zq4qhZX/RM/ tIgLB461eJ5V95iQDDc5Ibj9Cvy4vJfXLQRO0nLVQAT2Yz58tkEO1bDZpWOPAyUNneIL yhIWp+Spbuqh"
            ]
            ],
        'is_processed' => false,
        'fail_count' => 1,
        'created_at' => '2023-04-14',
        'updated_at' => NULL,
        'is_deleted' => 0,
    ];

    protected $payload1 = [
        'id' => 1,
        'payload' => 'payloadnew',
        'is_processed' => false,
        'fail_count' => 1,
        'created_at' => '2023-04-14',
        'updated_at' => NULL,
        'is_deleted' => 0,
    ];
    public function __construct(IEmailInfoRepository $emailInfoRepository, EmailInfoService $emailInfoService)
    {
        $this->emailInfoRepository = $emailInfoRepository;
        $this->emailInfoService = $emailInfoService;
    }

    public function postEmailInfo()
    {
        // $result = $this->emailInfoRepository->create($this->payload);
        // dd($result);

        $result = $this->emailInfoService->postEmailInfo($this->payload);
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
        $result = $this->emailInfoRepository->update(9, $this->payload);
        dd($result);
    }
}
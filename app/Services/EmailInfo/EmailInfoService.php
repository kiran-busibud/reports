<?php

namespace App\Services\EmailInfo;

use App\Repositories\EmailInfo\EmailInfoRepository;
use App\Repositories\EmailInfo\AttachmentRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use App\Services\EmailInfo\EmailParseService;


class EmailInfoService
{
    protected $emailInfoRepository;
    protected $attachmentRepository;

    protected $emailParseService;
    function __construct(EmailInfoRepository $emailInfoRepository, AttachmentRepository $attachmentRepository, EmailParseService $emailParseService){
        $this->emailInfoRepository = $emailInfoRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->emailParseService = $emailParseService;
    }

    function getTenantFromEmail(string $email)
    {
        $tenant = random_int(1,10);
        return $tenant;
    }

    // function postEmailInfo($payload)
    // {
    //     $envelope = $payload['payload']['envelope'];
    //     $toEmails = $envelope['to'];
    //     $payload['tenant'] = $this->getTenantFromEmail($toEmails[count($toEmails)-1]);
    //     $payload['payload'] = json_encode($payload['payload']);

    //     return $this->emailInfoRepository->create($payload);
    // }

    function postEmailInfo($payload)
    {
        foreach($payload as $key=>$value)
        {
            Log::info('e',[$key,$value]);
        }
        $envelope = json_decode($payload['envelope'],true);
        $toEmails = $envelope['to'];
        $payload['tenant'] = $this->getTenantFromEmail($toEmails[count($toEmails)-1]);
        $payload['payload'] = json_encode($payload);
        $payload['is_processed'] = false;
        $payload['fail_count'] = 0;
        $payload['is_deleted'] = false;

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

    function getUnprocessesEmailsWithAttachments()
    {
        $emailData = $this->emailInfoRepository->getUnprocessesEmailsWithAttachments();

        $emailWithAttachments = [];

        foreach($emailData as $data)
        {
            $batchNumber = $data->batch_number;

            if(isset($emailWithAttachments[$batchNumber]))
            {
                $file = UploadedFile::fake()->create($data->attachment_url);
                $emailWithAttachments[$batchNumber]['attachments'][] = $file;
            }
            else
            {
                $emailWithAttachments[$batchNumber]['payload'] = $data->payload;
                $emailWithAttachments[$batchNumber]['tenant'] = $data->tenant;

                $file = UploadedFile::fake()->create($data->attachment_url);

                $emailWithAttachments[$batchNumber]['attachments'][] = $file;
            }
        }

        foreach($emailWithAttachments as $emailWithAttachment)
        {
            $this->emailParseService->parse($emailWithAttachment['payload'], $emailWithAttachment['attachments'],$emailWithAttachment['tenant']);
        }
    }

}
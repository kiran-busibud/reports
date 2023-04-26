<?php

namespace App\Services\EmailInfo;

use App\Repositories\EmailInfo\IAttachmentRepository;
use Illuminate\Http\UploadedFile;
use App\Keys\EmailInfo\AttachmentKeys;
use App\Keys\EmailInfo\AttachmentMetaKeys;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class AttachmentService
{

    protected $attachmentRepository;
    protected $disk;

    public function __construct(IAttachmentRepository $attachmentRepository)
    {

        $this->attachmentRepository = $attachmentRepository;

        // $this->disk = Storage::disk();

    }

    public function uploadFile($attachment)
    {
        $path = $attachment->store('attachments', 'public');
        return $path;
    }

    public function addAttachmentsToTenantDirectory(array $attachments)
    {

        foreach ($attachments as $attachment) {

            $attachment = json_decode(json_encode($attachment), true);

            $tenantId = $attachment['tenant'];

            // Log::info('tenantId',[$tenantId]);
            // Log::info('attachment',[$attachment]);

            if ($tenantId == null)
            {
                $tenantId = 555;
            }

            Log::info('tenantId',[$tenantId]);

            $tenant = Tenant::find($tenantId);


            tenancy()->initialize($tenant);

            $file = UploadedFile::fake()->create($attachment['attachment_url']);

            $file->store('tenant' . $tenantId, 'public');

            $attachmentData = [];

            $attachmentData[AttachmentKeys::ATTACHMENT_URL] = $attachment[AttachmentKeys::ATTACHMENT_URL];
            $attachmentData[AttachmentKeys::BATCH_NUMBER] = $attachment[AttachmentKeys::BATCH_NUMBER];
            $attachmentData[AttachmentKeys::ORIGINAL_NAME] = $attachment[AttachmentKeys::ATTACHMENT_NAME];
            $attachmentData[AttachmentKeys::ATTACHMENT_NAME] = $attachment[AttachmentKeys::ATTACHMENT_NAME];
            $attachmentData[AttachmentKeys::ATTACHMENT_SIZE] = $attachment[AttachmentKeys::ATTACHMENT_SIZE];
            $attachmentData[AttachmentKeys::ATTACHMENT_TYPE] = $attachment[AttachmentKeys::ATTACHMENT_TYPE];
            $attachmentData[AttachmentKeys::EMBEDDED] = $attachment[AttachmentKeys::EMBEDDED];
            $attachmentData[AttachmentKeys::CONTENT_ID] = $attachment[AttachmentKeys::CONTENT_ID];
            $attachmentData[AttachmentKeys::ATTACHMENT_EXTENSION] = $attachment[AttachmentKeys::ATTACHMENT_EXTENSION];
            $attachmentData[AttachmentKeys::DELETED] = $attachment[AttachmentKeys::DELETED];
            $attachmentData[AttachmentKeys::FAILED] = $attachment[AttachmentKeys::FAILED];

            $status = $this->attachmentRepository->create($attachmentData);

            tenancy()->end();
        }
    }

    public function saveAttachments(array $attachments)
    {

        //unique id for batch number
        $batchNumber = Str::uuid()->toString();

        foreach ($attachments as $attachment) {

            $attachmentData = [];

            $embedded = 0;
            $contentId = $attachment['content-id'];

            // $file = $attachment['file'];

            $failed = false;

            if (!isset($attachment['file'])) {
                $failed = true;
            } else {
                $file = $attachment['file'];
            }
            $path = $this->uploadFile($file);

            $attachmentData[AttachmentKeys::ATTACHMENT_URL] = $path;
            $attachmentData[AttachmentKeys::BATCH_NUMBER] = $batchNumber;
            $attachmentData[AttachmentKeys::ORIGINAL_NAME] = $file->getClientOriginalName();
            $attachmentData[AttachmentKeys::ATTACHMENT_NAME] = $file->getClientOriginalExtension();
            $attachmentData[AttachmentKeys::ATTACHMENT_SIZE] = $file->getSize();
            $attachmentData[AttachmentKeys::ATTACHMENT_TYPE] = $file->getMimeType();
            $attachmentData[AttachmentKeys::EMBEDDED] = $embedded;
            $attachmentData[AttachmentKeys::CONTENT_ID] = $contentId;
            $attachmentData[AttachmentKeys::ATTACHMENT_EXTENSION] = $file->getClientOriginalExtension();
            $attachmentData[AttachmentKeys::DELETED] = 0;
            $attachmentData[AttachmentKeys::FAILED] = $failed;

            $status = $this->attachmentRepository->create($attachmentData);
            return $batchNumber;

        }




        // $attachmentData[AttachmentKeys::UPLOADED_DATE] = Carbon::now()->toDateTimeString();
        // $attachmentData[AttachmentKeys::UPLOADED_DATE_GMT] = Carbon::now('GMT')->toDateTimeString();
        $attachmentData[AttachmentKeys::DELETED] = 0;

        if (isset($attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT])) {
            $attachmentData[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT] = $attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT];
        }

        //storing in db
        $status = $this->attachmentRepository->create($attachmentData);

        $isPublic = false;

        if (isset($attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT]) && $attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT] == 1) {
            $isPublic = true;
        }

    }

}
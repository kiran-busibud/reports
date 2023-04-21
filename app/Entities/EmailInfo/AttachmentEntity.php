<?php
/**
 * This file contains AttachmentEntity class
 * 
 * 
 */
namespace App\Entities\EmailInfo;

use App\Keys\EmailInfo\AttachmentKeys;

class AttachmentEntity
{

    public $id;
    public $attachmentUrl;
    public $batchNumber;
    public $originalName;
    public $attachmentName;
    public $attachmentSize;
    public $attachmentType;
    public $embedded;
    public $contentId;
    public $attachmentExtension;
    public $uploadedDate;
    public $uploadedDateGmt;
    public $deleted;
    public $failed;

    public $metaData = [];

    /**
     * Cannot initialize directly
     * 
     */
    private function __costruct()
    {

    }

    public static function makeInstance(array $attachmentData=[])
    {
        $attachmentEntity = new AttachmentEntity();
        
        // set the instance variable
        $attachmentEntity->id = $attachmentData[AttachmentKeys::ID] ?? $attachmentEntity->id;
        $attachmentEntity->attachmentUrl = $attachmentData[AttachmentKeys::ATTACHMENT_URL];
        $attachmentEntity->batchNumber = $attachmentData[AttachmentKeys::BATCH_NUMBER];
        $attachmentEntity->originalName = $attachmentData[AttachmentKeys::ORIGINAL_NAME];
        $attachmentEntity->attachmentName = $attachmentData[AttachmentKeys::ATTACHMENT_NAME];
        $attachmentEntity->attachmentSize = $attachmentData[AttachmentKeys::ATTACHMENT_SIZE];
        $attachmentEntity->attachmentType = $attachmentData[AttachmentKeys::ATTACHMENT_TYPE];
        $attachmentEntity->embedded = $attachmentData[AttachmentKeys::EMBEDDED];
        $attachmentEntity->contentId = $attachmentData[AttachmentKeys::CONTENT_ID];
        $attachmentEntity->attachmentExtension = $attachmentData[AttachmentKeys::ATTACHMENT_EXTENSION];
        $attachmentEntity->uploadedDate = $attachmentData[AttachmentKeys::UPLOADED_DATE];
        $attachmentEntity->uploadedDateGmt = $attachmentData[AttachmentKeys::UPLOADED_DATE_GMT];
        $attachmentEntity->deleted = $attachmentData[AttachmentKeys::DELETED];
        $attachmentEntity->failed = $attachmentData[AttachmentKeys::FAILED];

        //return the instance created
        return $attachmentEntity;

    }
}

?>
<?php

/**
 * This file contains EmailAttachmentEntity class
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 * 
 */

namespace App\Entities\EmailInfo;

use App\Keys\EmailInfo\EmailAttachmentKeys;

class EmailAttachmentEntity
{

    public $id = null;
    public $payload = null;
    public $isProcessed = null;
    public $failCount = null;
    public $createdAt = null;
    public $updatedAt = null;
    public $isDeleted = null;
    public $tenant = null;
    public $metaData = [];

    /**
     * Making constructor private to prevent direct initialization of class 
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * This method creates instance of a EmailAttachmentEntity
     * 
     * @param $entityData array
     * 
     * @return EmailAttachmentEntity
     */
    public static function makeInstance(array $entityData = [])
    {
        if (empty($entityData)) {
            return false;
        }

        //create instance of EmailAttachmentEntity
        $entity = new EmailAttachmentEntity();

        //check if id is set in the data array which is passed to function, if not set,
        //set the default value which is null
        $entity->id
            = isset($entityData[EmailAttachmentKeys::ID])
            ? $entityData[EmailAttachmentKeys::ID] : $entity->id;

        $entity->payload = $entityData[EmailAttachmentKeys::PAYLOAD];
        $entity->isProcessed = $entityData[EmailAttachmentKeys::IS_PROCESSED];
        $entity->failCount = $entityData[EmailAttachmentKeys::FAIL_COUNT];
        $entity->createdAt = $entityData[EmailAttachmentKeys::CREATED_AT];
        $entity->updatedAt = $entityData[EmailAttachmentKeys::UPDATED_AT];
        $entity->isDeleted = $entityData[EmailAttachmentKeys::IS_DELETED];
        $entity->tenant = $entityData[EmailAttachmentKeys::TENANT];

        return $entity;
    }
}
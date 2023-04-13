<?php

/**
 * This file contains EmailInfoEntity class
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 * 
 */

namespace App\Entities\EmailInfo;

use App\Keys\EmailInfo\EmailInfoKeys;

class EmailInfoEntity
{

    public $id = null;
    public $payload = null;
    public $isProcessed = null;
    public $failCount = null;
    public $createdAt = null;
    public $updatedAt = null;
    public $isDeleted = null;
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
     * This method creates instance of a EmailInfoEntity
     * 
     * @param $entityData array
     * 
     * @return EmailInfoEntity
     */
    public static function makeInstance(array $entityData = [])
    {
        if (empty($entityData)) {
            return false;
        }

        //create instance of EmailInfoEntity
        $entity = new EmailInfoEntity();

        //check if id is set in the data array which is passed to function, if not set,
        //set the default value which is null
        $entity->id
            = isset($entityData[EmailInfoKeys::ID])
            ? $entityData[EmailInfoKeys::ID] : $entity->id;

        $entity->payload = $entityData[EmailInfoKeys::PAYLOAD];
        $entity->isProcessed = $entityData[EmailInfoKeys::IS_PROCESSED];
        $entity->failCount = $entityData[EmailInfoKeys::FAIL_COUNT];
        $entity->createdAt = $entityData[EmailInfoKeys::CREATED_AT];
        $entity->updatedAt = $entityData[EmailInfoKeys::UPDATED_AT];
        $entity->isDeleted = $entityData[EmailInfoKeys::IS_DELETED];

        return $entity;
    }
}
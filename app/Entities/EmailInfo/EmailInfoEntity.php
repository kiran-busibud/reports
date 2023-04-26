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

        $entity->payload = $entityData[EmailInfoKeys::PAYLOAD];
        $entity->isProcessed = $entityData[EmailInfoKeys::IS_PROCESSED];
        $entity->failCount = $entityData[EmailInfoKeys::FAIL_COUNT];
        $entity->isDeleted = $entityData[EmailInfoKeys::IS_DELETED];
        $entity->tenant = $entityData[EmailInfoKeys::TENANT];

        return $entity;
    }
}
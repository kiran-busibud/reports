<?php

/**
 * This file contains EmailInfoMetaEntity class
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 * 
 */

namespace App\Entities\EmailInfo;

use App\Keys\EmailInfo\EmailInfoMetaKeys;

class EmailInfoMetaEntity
{

    public $id = null;
    public $emailInfoId = null;
    public $metaKey = null;
    public $metaValue = null;

    /**
     * Making constructor private to prevent direct initialization of class 
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * This method creates instance of the EmailInfoMetaEntity
     * 
     * @param $metaEntityData array
     * 
     * @return EmailInfoMetaEntity
     */
    public static function makeInstance(array $metaEntityData = [])
    {

        //check if empty array is passed
        if (empty($metaEntityData)) {
            //array is empty, return back, do not proceed
            return false;
        }

        //create instance of the EmailInfoMetaEntity
        $metaEntity = new EmailInfoMetaEntity();

        //check if Meta id is set, if not set, then set the default value of meta id,
        //which is null
        $metaEntity->id = isset($metaEntityData[EmailInfoMetaKeys::ID]) ? $metaEntityData[EmailInfoMetaKeys::ID] : $metaEntity->id;

        $metaEntity->emailInfoId = isset($metaEntityData[EmailInfoMetaKeys::EMAIL_INFO_ID]) ? $metaEntityData[EmailInfoMetaKeys::EMAIL_INFO_ID] : $metaEntity->emailInfoId;

        $metaEntity->metaKey = $metaEntityData[EmailInfoMetaKeys::META_KEY];
        $metaEntity->metaValue = $metaEntityData[EmailInfoMetaKeys::META_VALUE];

        return $metaEntity;
    }
}
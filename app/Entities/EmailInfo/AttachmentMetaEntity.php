<?php

/**
 * This file contains AttachmentMetaEntity class for hl_attachment_meta
 * 
 * @author Yuvaraj N <yuvaraj@zinosi.com>
 */

namespace App\Entities\EmailInfo;

use App\Keys\EmailInfo\AttachmentMetaKeys;

class AttachmentMetaEntity
{

    public $metaId;
    public $attachmentId;
    public $metaKey;
    public $metaValue;


   /**
     * Making constructor private to prevent direct initialization of class 
     *
     * @return void
     */
    private function __construct()
    {
    }    

    /**
     * This method creates instance of a AttachmentMetaEntity
     * 
     * @param $metaData array
     * 
     * @return AttachmentMetaEntity
     */
    public static function makeInstance(array $metaData = [])
    {
        if (empty($metaData)) {
            return false;
        }

        //create instance of AttachmentMetaEntity
        $metaEntity = new AttachmentMetaEntity();

        $metaEntity->metaId = $metaData[AttachmentMetaKeys::META_ID] ?? null;
        $metaEntity->attachmentId = $metaData[AttachmentMetaKeys::ATTACHMENT_ID];
        $metaEntity->metaKey = $metaData[AttachmentMetaKeys::META_KEY];
        $metaEntity->metaValue = $metaData[AttachmentMetaKeys::META_VALUE];

        return $metaEntity;
    }

}
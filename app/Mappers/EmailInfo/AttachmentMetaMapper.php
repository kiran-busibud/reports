<?php

/**
 * This file contains implementation class for IAttachmentMetaMapper
 * 
 * @author Yuvaraj N <yuvaraj@zinosi.com>
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\AttachmentMetaEntity;
use App\Keys\EmailInfo\AttachmentMetaKeys;
use App\Models\AttachmentMetaModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * AttachmentMetaMapper
 */
class AttachmentMetaMapper implements IAttachmentMetaMapper
{

    /**
     * This method persists a AttachmentMetaEntity to the database
     * 
     * @param AttachmentMetaEntity $metaEntity object 
     * 
     * @return boolean
     */
    public function add(AttachmentMetaEntity $metaEntity)
    {

        //create instance of AttachmentMetaModel
        $attachmentMeta = new AttachmentMetaModel();

        $attachmentMeta->attachment_id = $metaEntity->attachmentId;
        $attachmentMeta->meta_key = $metaEntity->metaKey;
        $attachmentMeta->meta_value = $metaEntity->metaValue;

        //return the boolean for the save operation
        return $attachmentMeta->save();
    }

    /**
     * This method deletes a AttachmentMetaEntity from database
     * 
     * @param AttachmentMetaEntity $metaEntity
     * 
     * @return boolean
     * 
     */
    public function delete(AttachmentMetaEntity $metaEntity)
    {
        try {
            //fetch the record for metaId from database
            $attachmentMetaModel = AttachmentMetaModel::findOrFail($metaEntity->metaId);
        } catch (ModelNotFoundException $e) {
            //no data founded for the particular metaId
            return false;
        }
        //delete the row from database
        $success = $attachmentMetaModel->delete();

        //return the bool value indicating success or failure
        return $success;
    }

    /**
     * This method fetches AttachmentMetaEntity from the database
     * 
     * @param AttachmentMetaEntity $metaEntity
     * @param $metaId int|string
     * @param $attachmentIds array
     * 
     * @return array|AttachmentMetaEntity
     * 
     */
    public function get($attachmentEntity = null, $metaId = false,array $attachmentIds = [])
    {

        //if metaId is not provided, then fetch all the meta objects for the AttachmentMetaEntity Instance
        if (!$metaId) {

            if(!empty($attachmentIds)){

                $attachmentMetaData = AttachmentMetaModel::whereIn(AttachmentMetaKeys::ATTACHMENT_ID, $attachmentIds)->get()->toArray();
            }else{

                //Fetch all meta data for the particular AttachmentMetaEntity instance
                $attachmentMetaData = AttachmentMetaModel::where(AttachmentMetaKeys::ATTACHMENT_ID, $attachmentEntity->id)->get()->toArray();

            }

            //create empty array to store AttachmentMetaEntity instances
            $attachmentMetaObjects = [];

            //loop over fetched meta data and create AttachmentMetaEntity instances
            foreach ($attachmentMetaData as $metaData) {

                //push AttachmentMetaEntity instance 
                $attachmentMetaObjects[] = AttachmentMetaEntity::makeInstance($metaData);
            }

            return $attachmentMetaObjects;

        } else {
            //metaId is provided so fetch the corresponding AttachmentMetaEntity instance
            try {
                //fetch meta data key value pair for particular metaId
                $attachmentMetaDataObject = AttachmentMetaModel::findOrFail($metaId);
            } catch (ModelNotFoundException $e) {
                //no meta key value pair founded for provided id
                return false;
            }


            return AttachmentMetaEntity::makeInstance($attachmentMetaDataObject->toArray());
        }
    }

    /**
     * This method updates AttachmentMetaEntity in database
     * 
     * @param AttachmentMetaEntity $metaEntity object
     * 
     * @return bool
     * 
     */
    public function update(AttachmentMetaEntity $metaEntity)
    {
        try {
            //fetch the record for metaId from database
            $attachmentMetaModel = AttachmentMetaModel::findOrFail($metaEntity->metaId);
            
        } catch (ModelNotFoundException $e) {
            //no data founded for the particular metaId
            return false;
        }

        //updating the meta value for the metaId
        $attachmentMetaModel->meta_value = $metaEntity->metaValue;

        //saving the changes to the database
        $success = $attachmentMetaModel->save();

        return $success;
    }
}
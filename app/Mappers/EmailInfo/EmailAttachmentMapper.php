<?php

/**
 * This file contains implementation class for IEmailAttachmentMapper
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\EmailAttachmentEntity;
use App\Keys\EmailInfo\EmailInfoMetaKeys;
use App\Models\EmailInfoMetaModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmailAttachmentMapper implements IEmailAttachmentMapper
{

    /**
     * This method persists a EmailAttachmentEntity to the database
     * 
     * @param EmailAttachmentEntity $emailAttachmentEntity object 
     * 
     * @return boolean
     */
    public function add(EmailAttachmentEntity $emailAttachmentEntity)
    {

        //create instance of EmailInfoMetaModel
        $emailAttachmentModel = new EmailInfoMetaModel();

        $emailAttachmentModel->email_info_id = $emailAttachmentEntity->emailInfoId;
        $emailAttachmentModel->meta_key = $emailAttachmentEntity->metaKey;
        $emailAttachmentModel->meta_value = $emailAttachmentEntity->metaValue;

        //return the boolean for the save operation
        return $emailAttachmentModel->save();
    }

    /**
     * This method deletes a EmailAttachmentEntity from database
     * 
     * @param EmailAttachmentEntity $emailAttachmentEntity
     * 
     * @return boolean
     * 
     */
    public function delete(EmailAttachmentEntity $emailAttachmentEntity)
    {
        try {
            //fetch the record for attachmentId from database
            $emailAttachmentModel = EmailInfoMetaModel::findOrFail($emailAttachmentEntity->id);
        } catch (ModelNotFoundException $e) {
            //no data founded for the particular attachmentId
            return false;
        }
        //delete the row from database
        $success = $emailAttachmentModel->delete();

        //return the bool value indicating success or failure
        return $success;
    }

    /**
     * This method fetches EmailInfoEntity from the database
     * 
     * @param EmailAttachmentEntity $emailAttachmentEntity
     * @param $attachmentId int|string
     * 
     * @return array|EmailAttachmentEntity
     * 
     */
    public function get($entity = null, $attachmentId = false)
    {

        //if attachmentId is not provided, then fetch all the meta objects for the EmailAttachmentEntity Instance
        if (!$attachmentId) {

            //Fetch all meta data for the particular EmailAttachmentEntity instance
            $metaData = EmailInfoMetaModel::where(EmailInfoMetaKeys::EMAIL_INFO_ID, $entity->id)->get()->toArray();


            //create empty array to store EmailAttachmentEntity instances
            $metaObjects = [];

            //loop over fetched meta data and create EmailAttachmentEntity instances
            foreach ($metaData as $eachMetaData) {

                //push EmailAttachmentEntity instance 
                $metaObjects[] = EmailAttachmentEntity::makeInstance($eachMetaData);
            }

            return $metaObjects;

        } else {
            //attachmentId is provided so fetch the corresponding EmailAttachmentEntity instance
            try {
                //fetch meta data key value pair for particular attachmentId
                $emailAttachmentModel = EmailInfoMetaModel::findOrFail($attachmentId);
            } catch (ModelNotFoundException $e) {
                //no meta key value pair founded for provided id
                return false;
            }


            return EmailAttachmentEntity::makeInstance($emailAttachmentModel->toArray());
        }
    }

    /**
     * This method updates EmailAttachmentEntity in database
     * 
     * @param EmailAttachmentEntity $emailAttachmentEntity object
     * 
     * @return bool
     * 
     */
    public function update(EmailAttachmentEntity $emailAttachmentEntity)
    {
        try {
            //fetch the record for attachmentId from database
            $emailAttachmentModel = EmailInfoMetaModel::findOrFail($emailAttachmentEntity->emailInfoId);
            
        } catch (ModelNotFoundException $e) {
            //no data founded for the particular attachmentId
            return false;
        }

        //updating the meta value for the attachmentId
        $emailAttachmentModel->meta_value = $emailAttachmentEntity->metaValue;

        //saving the changes to the database
        $success = $emailAttachmentModel->save();

        return $success;
    }
}
<?php

/**
 * This file contains implementation class for IEmailInfoMetaMapper
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\EmailInfoMetaEntity;
use App\Keys\EmailInfo\EmailInfoMetaKeys;
use App\Models\EmailInfoMetaModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmailInfoMetaMapper implements IEmailInfoMetaMapper
{

    /**
     * This method persists a EmailInfoMetaEntity to the database
     * 
     * @param EmailInfoMetaEntity $metaEntity object 
     * 
     * @return boolean
     */
    public function add(EmailInfoMetaEntity $metaEntity)
    {

        //create instance of EmailInfoMetaModel
        $metaModel = new EmailInfoMetaModel();

        $metaModel->email_info_id = $metaEntity->emailInfoId;
        $metaModel->meta_key = $metaEntity->metaKey;
        $metaModel->meta_value = $metaEntity->metaValue;

        //return the boolean for the save operation
        return $metaModel->save();
    }

    /**
     * This method deletes a EmailInfoMetaEntity from database
     * 
     * @param EmailInfoMetaEntity $metaEntity
     * 
     * @return boolean
     * 
     */
    public function delete(EmailInfoMetaEntity $metaEntity)
    {
        try {
            //fetch the record for metaId from database
            $metaModel = EmailInfoMetaModel::findOrFail($metaEntity->id);
        } catch (ModelNotFoundException $e) {
            //no data founded for the particular metaId
            return false;
        }
        //delete the row from database
        $success = $metaModel->delete();

        //return the bool value indicating success or failure
        return $success;
    }

    /**
     * This method fetches EmailInfoEntity from the database
     * 
     * @param EmailInfoMetaEntity $metaEntity
     * @param $metaId int|string
     * 
     * @return array|EmailInfoMetaEntity
     * 
     */
    public function get($entity = null, $metaId = false)
    {

        //if metaId is not provided, then fetch all the meta objects for the EmailInfoMetaEntity Instance
        if (!$metaId) {

            //Fetch all meta data for the particular EmailInfoMetaEntity instance
            $metaData = EmailInfoMetaModel::where(EmailInfoMetaKeys::EMAIL_INFO_ID, $entity->id)->get()->toArray();


            //create empty array to store EmailInfoMetaEntity instances
            $metaObjects = [];

            //loop over fetched meta data and create EmailInfoMetaEntity instances
            foreach ($metaData as $eachMetaData) {

                //push EmailInfoMetaEntity instance 
                $metaObjects[] = EmailInfoMetaEntity::makeInstance($eachMetaData);
            }

            return $metaObjects;

        } else {
            //metaId is provided so fetch the corresponding EmailInfoMetaEntity instance
            try {
                //fetch meta data key value pair for particular metaId
                $metaModel = EmailInfoMetaModel::findOrFail($metaId);
            } catch (ModelNotFoundException $e) {
                //no meta key value pair founded for provided id
                return false;
            }


            return EmailInfoMetaEntity::makeInstance($metaModel->toArray());
        }
    }

    /**
     * This method updates EmailInfoMetaEntity in database
     * 
     * @param EmailInfoMetaEntity $metaEntity object
     * 
     * @return bool
     * 
     */
    public function update(EmailInfoMetaEntity $metaEntity)
    {
        try {
            //fetch the record for metaId from database
            $metaModel = EmailInfoMetaModel::findOrFail($metaEntity->emailInfoId);
            
        } catch (ModelNotFoundException $e) {
            //no data founded for the particular metaId
            return false;
        }

        //updating the meta value for the metaId
        $metaModel->meta_value = $metaEntity->metaValue;

        //saving the changes to the database
        $success = $metaModel->save();

        return $success;
    }
}
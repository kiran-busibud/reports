<?php
/**
 * This file contains implementation class for interface IAttachmentMapper
 * 
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\AttachmentEntity;
use App\Keys\EmailInfo\AttachmentKeys;
use App\Mappers\EmailInfo\IAttachmentMapper;
use App\Models\AttachmentModel;
use Illuminate\Support\Facades\DB;

class AttachmentMapper implements IAttachmentMapper
{
    /**
     * This method adds a attachment entity to database
     * 
     * @param AttachmentEntity $attachment
     * 
     * @return mixed 
     * 
     */
    public function add(AttachmentEntity $attachmentEntity)
    {
        // create a attachment model
        $attachmentModel = new AttachmentModel();
        //map attachment entity to attachment model
        $attachmentModel = $this->mapAttachmentToModel($attachmentEntity, $attachmentModel);
        //save the model
        $success = $attachmentModel->save();  

        if($success)
            $success = $attachmentModel->id;
        //returns false or insertion id
        return $success;    
    }

    /**
     * This method gets attachment data from database
     * 
     * @param string $id
     * @param string $batchNumber 
     * @param int $start
     * @param int $limit
     * @param bool $isAll
     * 
     * @return array|AttachmentEntity
     */
    public function get($id, $batchNumber=false,$attachmentName = false,$start = false, $limit = false, $isAll = false)
    {

        $attachmentData = null;

        if(isset($id)){
            //fetching attachment data by id
            $attachmentData = AttachmentModel::where(AttachmentKeys::ID,$id)->get()->first();

        }else  if($attachmentName){

            $attachmentData = AttachmentModel::where(AttachmentKeys::ATTACHMENT_NAME,$attachmentName)
                                            ->where(AttachmentKeys::DELETED,0)->get()->first();
        } else if($batchNumber){

                //fetching attachment data by batch number
                $attachmentData = AttachmentModel::where(AttachmentKeys::BATCH_NUMBER,$batchNumber)
                                                ->where(AttachmentKeys::DELETED,0)->get();

        //checking if get all is true
        }else if($isAll){
            //fetching all the attachment data's
            $attachmentData = AttachmentModel::all()->where(AttachmentKeys::DELETED,0);

        }
        //checking if limit are set
        else if($start || $limit){
            if($limit && $start){

                $attachmentData =  AttachmentModel::where(AttachmentKeys::DELETED,0)->skip($start)->take($limit)->get();
            }else if($limit){

                $attachmentData =  AttachmentModel::where(AttachmentKeys::DELETED,0)->skip(0)->take($limit)->get();
            }else{

                $attachmentData =  AttachmentModel::where(AttachmentKeys::DELETED,0)->skip($start)->get();
            }
        }

        // attachment not found
        if(!$attachmentData){
            return false;
        }

        $attachmentData = $attachmentData->toArray();

        //if array empty
        if(empty($attachmentData)){
            return false;
        }

        //for storing attachmentEnitity objects
        $attachmentEntityList = [];

        $isSingleRecord = false;      

        if($id || $attachmentName){

            $isSingleRecord = true;
            $attachmentData = [$attachmentData];
        }


        //storing attachment ids for getting meta data 
        $attachmentIds = [];

        foreach($attachmentData as $attachment){

            //creating an attachmentEntity instance with $attachment data
            $attachmentEntity = AttachmentEntity::makeInstance($attachment);
            $attachmentIds[] = $attachmentEntity->id;

            $attachmentEntityList[] = $attachmentEntity;
        }

        //getting all meta data for attachmentIds
        $attachmentMetaEntityList = (new AttachmentMetaMapper)->get(null,false, $attachmentIds);
        
        //if not meta data found
        if(empty($attachmentMetaEntityList)){
            $attachmentMetaEntityList = [];
        }
        
        foreach($attachmentEntityList as $attachmentEntity){
            //assigning empty metaData array to attachment entity
            $attachmentEntity->metaData = [];
            
            foreach($attachmentMetaEntityList as $metaEntity){
                //checking if attachment id and meta data attachment id is equal
                if($attachmentEntity->id == $metaEntity->attachmentId){
                    //storing the meta data
                    $attachmentEntity->metaData[$metaEntity->metaKey] = [
                        'id'=> $metaEntity->metaKey,
                        'value'=>$metaEntity->metaValue,
                    ];
                }
            }
        }
        
        if($isSingleRecord){

            return $attachmentEntityList[0];
        }

        return $attachmentEntityList;

    }


    /**
     * Delete a attachment
     * 
     * @param AttachmentEntity $attachment
     * @param string $id
     * 
     */
    public function delete($id, $batchNumber=false)
    {
        if($id){

            // return AttachmentModel::where(AttachmentKeys::ID,$id)->delete();
            $attachmentModel = AttachmentModel::where(AttachmentKeys::ID,$id)->get()->first();

            if(!$attachmentModel)
                return false;
    
            //updating deleted status
            $attachmentModel->deleted = 1;

            //save data
            $success = $attachmentModel->save();

            return $success;

        }else{

            $attachmentModelList = AttachmentModel::where(AttachmentKeys::BATCH_NUMBER,$batchNumber)->get();

            if(!$attachmentModelList)
                return false;

            $attachmentModelList = $attachmentModelList->all();
            
            
            foreach($attachmentModelList as $attachmentModel){
               
                //updating deleted status
                $attachmentModel->deleted = 1;

                //save data
                $success = $attachmentModel->save();


            }
            
            return true;

        }
    }

    /**
     * Updates a attachment
     * 
     * @param AttachmentEntity $attachment
     * @param string $id
     * 
     */
    public function update(AttachmentEntity $attachment, $id)
    {
        if(!isset($id)){

            $attachmentModel = AttachmentModel::where(AttachmentKeys::ID,$id)->get()->first();
        }else{
            $attachmentModel = AttachmentModel::where(AttachmentKeys::ID,$attachment->id)->get()->first();

        }

        if(!$attachmentModel)
            return false;

        //map the updated attachment data to the model
        $attachmentModel = $this->mapAttachmentToModel($attachment, $attachmentModel);
        //save data
        $success = $attachmentModel->save();

        return $success;
    }

    /**
     * This method is used to get an attachment by a column
     * 
     * @param string $columnName
     * @param string $columnValue
     * 
     * @return bool|AttachmentEntity
     * 
     */
    public function getByColumnMultiple($columnName, $columnValue){
        
        $model = AttachmentModel::where('deleted', '0')->whereIn($columnName, $columnValue)->get();
        //check if attachment is fetched or not
        if(!$model){
            //no attachment fetched, so return false;
            return false;
        }

        $entities = [];

        foreach($model as $singleAttachment){
            $entities[] = AttachmentEntity::makeInstance($singleAttachment->toArray());
        }

        return $entities;

    }

    /**
     * This method maps the AttachmentEntity to Attachment model
     * 
     * @param AttachmentEntity $attachmentEntity
     * @param AttachmentModel $attachmentModel
     * 
     */
    private function mapAttachmentToModel(AttachmentEntity $attachmentEntity, AttachmentModel $attachmentModel)
    {

        $attachmentModel->id = $attachmentEntity->id;
        $attachmentModel->attachment_url = $attachmentEntity->attachmentUrl;
        $attachmentModel->batch_number = $attachmentEntity->batchNumber;
        $attachmentModel->original_name = $attachmentEntity->originalName;
        $attachmentModel->attachment_name = $attachmentEntity->attachmentName;
        $attachmentModel->attachment_size = $attachmentEntity->attachmentSize;
        $attachmentModel->attachment_type = $attachmentEntity->attachmentType;
        $attachmentModel->embedded = $attachmentEntity->embedded;
        $attachmentModel->content_id = $attachmentEntity->contentId;
        $attachmentModel->attachment_extension = $attachmentEntity->attachmentExtension;
        $attachmentModel->uploaded_date = $attachmentEntity->uploadedDate;
        $attachmentModel->uploaded_date_gmt = $attachmentEntity->uploadedDateGmt;
        $attachmentModel->deleted = $attachmentEntity->deleted;
        $attachmentModel->failed = $attachmentEntity->failed;
        //return mapped model
        return $attachmentModel;
    }

    public function mapMultipleBatchNumberIntoOne(array $batchNumbers)
    {
        $assignedbatchNumber = $batchNumbers[0]; // 1st batch number is used to map to all others
        // $sql = "UPDATE `hl_attachments` SET `batch_number` = '".$assignedbatchNumber."' WHERE `batch_number` IN ".$batchNumbers."";
        foreach($batchNumbers as $batchNumber){
            $sql =  "UPDATE `hl_attachments` SET `batch_number` = '".$assignedbatchNumber."' WHERE `batch_number` = '$batchNumber'";
            $result = DB::update($sql);
        }
        // file_put_contents("testingMiddleWareData.txt", "SQL:: ".json_encode($sql)."\n\n", FILE_APPEND);
        // $result = DB::update($sql);
        // if($result){
            return $assignedbatchNumber;
        // }
        // return false;
    }
}
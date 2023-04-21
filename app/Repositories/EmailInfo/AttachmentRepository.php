<?php

namespace App\Repositories\EmailInfo;

use App\Entities\EmailInfo\AttachmentEntity;
use App\Entities\EmailInfo\AttachmentMetaEntity;
use App\Keys\EmailInfo\AttachmentKeys;
use App\Keys\EmailInfo\AttachmentMetaKeys;
use App\Mappers\EmailInfo\AttachmentMapper;
use App\Mappers\EmailInfo\AttachmentMetaMapper;
use Illuminate\Support\Facades\Log;


class AttachmentRepository implements IAttachmentRepository
{
    protected $attachmentMapper;
    protected $attachmentMetaMapper;

    private $attachmentEntity = [
      
        AttachmentKeys::ID,
        AttachmentKeys::ATTACHMENT_URL,
        AttachmentKeys::BATCH_NUMBER,
        AttachmentKeys::ORIGINAL_NAME,
        AttachmentKeys::ATTACHMENT_NAME,
        AttachmentKeys::ATTACHMENT_SIZE,
        AttachmentKeys::ATTACHMENT_TYPE,
        AttachmentKeys::EMBEDDED,
        AttachmentKeys::CONTENT_ID,
        AttachmentKeys::ATTACHMENT_EXTENSION,
        AttachmentKeys::UPLOADED_DATE,
        AttachmentKeys::UPLOADED_DATE_GMT,
        AttachmentKeys::DELETED,
    ];

    public function __construct() {

        $this->attachmentMapper = new AttachmentMapper();
        $this->attachmentMetaMapper = new AttachmentMetaMapper();
    }

    public function createOrUpdate($id = null){

    }

    /**
     * Get all items/rows of this entity
     *
     * @return array
     */
    public function getAll($start=false, $limit=false)
    {
        if($start && $limit){

            $attachmentData = $this->attachmentMapper->get(null,false,false, $start,$limit,false);
        }else{

            $attachmentData = $this->attachmentMapper->get(null, false, false,$start,$limit,true);
        }

        return $attachmentData;

    }

    /**
     * Get By Id
     *
     * @param string $id Id of the entity to be fetched
     * 
     * @return AttachmentEntity|AttachmentEntity[]|bool
     */
    public function getById(string $id, $ids = []){

        if(!empty($ids)){
            return $this->attachmentMapper->getByColumnMultiple(AttachmentKeys::ID, $ids);
        }

        //getting attachment data
        $attachmentData = $this->attachmentMapper->get($id);

        return $attachmentData;
    }

    /**
     * Get by batch Number
     * 
     * @param string $batchNumber of the entity
     * 
     * @return array
     */
    public function getByBatchNumber(string $batchNumber){

        //getting attachment data
        $attachmentData = $this->attachmentMapper->get(null,$batchNumber);

        return $attachmentData;
    } 

    /**
     * Checking if attachment exists on 
     * 
     * @return AttachmentEntity
     */
    public function getByAttachmentName($attachmentName){

        //getting attachment data
        $attachmentData = $this->attachmentMapper->get(null,null,$attachmentName);

        return $attachmentData;
    }  


    /**
     * Create new entity
     *
     * @param $attachmentData array
     * 
     * @return AttachmentEntity Newly created entity
     */
    public function create(array $attachmentData = []){
        
        //if attachment data empty return
        if(empty($attachmentData)){
            return false;
        }
        
        $attachmentData = $this->setDateTime($attachmentData);
        
        //seperate entity and meta data
        $keys = $this->seprateEntityAndMeta($attachmentData);

        //if attachment entity is empty return
        if(empty($keys['entityData'])){
            return false;
        }

        $attachmentEntity = AttachmentEntity::makeInstance($keys['entityData']);

        //add and get insertion id
        $attachmentId = $this->attachmentMapper->add($attachmentEntity);
        
        // Log::info('attachmentId',[$attachmentId]);
        
        
        //if insertion false return
        if(!$attachmentId){
            return false;
        }

        //if meta data exists add to meta table 
         if(!empty($keys['metaData'])){
            //metaData is present, so add the meta data for the particular message

            //iterate over all the meta data and persist the meta data
            foreach($keys['metaData'] as $metaKey => $metaValue){

                //payload for creating the AttachmentMetaEntity
                $payload = [
                    AttachmentMetaKeys::META_KEY => $metaKey,
                    AttachmentMetaKeys::ATTACHMENT_ID => $attachmentId,
                    AttachmentMetaKeys::META_VALUE => $metaValue
                ];

                //create instance of AttachmentMetaEntity with the payload
                $metaEntity = AttachmentMetaEntity::makeInstance($payload);

                //persist the AttachmentMetaEntity
                $this->attachmentMetaMapper->add($metaEntity);

            }
        }


        $attachmentEntity = $this->attachmentMapper->get($attachmentId);

        return $attachmentEntity;
    }

    /**
     * Update the entity by id
     *
     * @param string $id      Id of the entity to be updated
     * @param array  $payload Data to be updated for the entity
     * 
     * @return bool updated entity status
     */
    public function update(string $id, array $payload, bool $canAddNew = false)
    {

        //if payload empty return
        if(empty($payload)){
            return false;
        }

        $payload = $this->setDateTime($payload);

        //seperate entity and meta data
        $keys = $this->seprateEntityAndMeta($payload);

        //geting instance
        $attachmentEntity = $this->attachmentMapper->get($id);

        //if no attachment found return
        if(!$attachmentEntity){
            return false;
        }

        //mapping the entity with attachmentEntity
        foreach($keys['entityData'] as $key=>$value){
           $this->mapKeyToFunction($attachmentEntity,$key, $value);
        }

        //updating attachment data 
        $updateStatus= $this->attachmentMapper->update($attachmentEntity, $id);

        //update status false
        if(!$updateStatus){
            return false;
        }

        if(!empty($keys['metaData'])){

            //iterate over all the metaData passed as payload
            foreach($keys['metaData'] as $key => $value){
                
                //check if the metakey is valid meta key
                if(isset($attachmentEntity->metaData[$key])){
                    //key is valid meta key, so update the metaData

                    //create instance of the metaEntity with the metaId
                    $metaEntity = $this->attachmentMetaMapper->get($attachmentEntity, $attachmentEntity->metaData[$key]['id']);
                    //assign new meta value
                    $metaEntity->metaValue = $value;

                    //persist the updated meta values
                    $this->attachmentMetaMapper->update($metaEntity);

                }
                else
                {
                    //key does not exists

                    //check if new key can be added
                    if($canAddNew){
                        //create new meta entry 

                        //payload for new metaEntity creation
                        $payload = [
                            AttachmentMetaKeys::META_KEY => $key,
                            AttachmentMetaKeys::ATTACHMENT_ID => $attachmentEntity->id,
                            AttachmentMetaKeys::META_VALUE => $value
                        ];
                        //creating instance of AttachmentMetaEntity
                        $metaEntity = AttachmentMetaEntity::makeInstance($payload);

                        //persist the metaEntity
                        $this->attachmentMetaMapper->add($metaEntity);

                    }
                }

            }
        }


        return $updateStatus;
    }

    /**
     * Delete entity by id
     *
     * @param string $id The entity to be deleted
     * 
     * @return bool Deleted entity status
     */
    public function delete(string $id): bool
    {
        //attachment deleting attachment
        $status = $this->attachmentMapper->delete($id);
        
        //deleting in meta table 
        // $this->attachmentMetaMapper->deleteByAttachmentId($attachment->id);
        return $status;

    }

     /**
     * Delete entity by batch number
     *
     * @param string $batchNumber 
     * 
     * @return bool Deleted entity status
     */
    public function deleteByBatchNumber(string $batchNumber): bool
    {
        //attachment deleting attachment
        $status = $this->attachmentMapper->delete(null, $batchNumber);
        
        //deleting in meta table 
        // $this->attachmentMetaMapper->deleteByAttachmentId($attachment->id);
        return $status;

    }

    /**
     * This method get's attachments using the content ids passed
     * 
     * @param array $contentIds
     * 
     */
    public function getByContentIds(array $contentIds){

        $attachments = $this->attachmentMapper->getByColumnMultiple(AttachmentKeys::CONTENT_ID, $contentIds);

        return $attachments;

    }

    /**
     * This method get's attachments using multiple ids
     * 
     * @param array $attachmentIds
     * 
     */
    public function getByAttachmentIds(array $attachmentIds){

        $attachments = $this->attachmentMapper->getByColumnMultiple(AttachmentKeys::ID, $attachmentIds);

        return $attachments;

    }

    /**
     * This is a helper function to map entityData to attachmentEntity class
     * 
     * @param AttachmentEntity $attachmentEntity
     * @param string $key
     * @param string $value
     */
    private function mapKeyToFunction(AttachmentEntity $attachmentEntity, $key, $value){

        //assigning 
        switch($key){
            
            case AttachmentKeys::ID:
                $attachmentEntity->id = $value;break;
            case AttachmentKeys::ATTACHMENT_URL:
                $attachmentEntity->attachmentUrl = $value;break;
            case AttachmentKeys::BATCH_NUMBER:
                $attachmentEntity->batchNumber = $value;break;
            case AttachmentKeys::ORIGINAL_NAME:
                $attachmentEntity->originalName = $value;break;
            case AttachmentKeys::ATTACHMENT_NAME:
                $attachmentEntity->attachmentName = $value;break;
            case AttachmentKeys::ATTACHMENT_SIZE:
                $attachmentEntity->attachmentSize = $value;break;
            case AttachmentKeys::ATTACHMENT_TYPE:
                $attachmentEntity->attachmentSize = $value;break;
            case AttachmentKeys::EMBEDDED:
                $attachmentEntity->embedded = $value;break;
            case AttachmentKeys::CONTENT_ID:
                $attachmentEntity->contentId = $value;break;
            case AttachmentKeys::ATTACHMENT_EXTENSION:
                $attachmentEntity->attachmentExtension = $value;break;
            case AttachmentKeys::UPLOADED_DATE:
                $attachmentEntity->uploadedDate = $value;break;
            case AttachmentKeys::UPLOADED_DATE_GMT:
                $attachmentEntity->uploadedDateGmt = $value;break;
            case AttachmentKeys::DELETED:
                $attachmentEntity->deleted = $value;break;

        }
    }

    /**
     * This function set's datetime for the payload
     * 
     * @param array $payload
     * 
     * @return array
     * 
     */
    private function setDateTime($payload){

        $dateTime = new \DateTime();
        
        $gmtDateTime = $dateTime->format('Y-m-d H:i:s');
        
        $dateTime->setTimezone(new \DateTimeZone('Asia/Kolkata'));

        $companyDateTime = $dateTime->format('Y-m-d H:i:s');

        $payload[AttachmentKeys::UPLOADED_DATE] = $companyDateTime;
        
        $payload[AttachmentKeys::UPLOADED_DATE_GMT] = $gmtDateTime;

        return $payload;

    }


    /**
     * This is a helper function to seperate Entity and Meta 
     * 
     * @param array $data
     * @return array
     */
    private function seprateEntityAndMeta(array $data=[]){

        $entityData = [];
        $metaData = [];
        foreach($data as $key=>$value){
            if(in_array($key,$this->attachmentEntity)){
                $entityData[$key] = $value;
            }else{
                $metaData[$key] = $value;
            }
        }

        return ['entityData'=>$entityData, 'metaData'=>$metaData];
    }

    public function mapMultipleBatchNumberIntoOne(array $batchNumbers)
    {
        return $this->attachmentMapper->mapMultipleBatchNumberIntoOne($batchNumbers);
    }
}

?>
<?php

/**
 * This file contains EmailInfoRepository class
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 */
namespace App\Repositories\EmailInfo;

use App\Entities\EmailInfo\EmailInfoMetaEntity;
use App\Entities\EmailInfo\EmailInfoEntity;
use App\Keys\EmailInfo\EmailInfoMetaKeys;
use App\Keys\EmailInfo\EmailInfoKeys;
use App\Mappers\EmailInfo\EmailInfoMapper;
use App\Mappers\EmailInfo\EmailInfoMetaMapper;
use App\Repositories\EmailInfo\IEmailInfoRepository;

class EmailInfoRepository implements IEmailInfoRepository
{

    private $allowedEntityValues = [
        EmailInfoKeys::ID,
        EmailInfoKeys::PAYLOAD,
        EmailInfoKeys::IS_PROCESSED,
        EmailInfoKeys::FAIL_COUNT,
        EmailInfoKeys::CREATED_AT,
        EmailInfoKeys::UPDATED_AT,
        EmailInfoKeys::IS_DELETED,
        EmailInfokeys::TENANT,
    ];

    private $EmailInfoMapper;
    private $EmailInfoMetaMapper;

    public function __construct(){

        $this->EmailInfoMapper = new EmailInfoMapper();

        $this->EmailInfoMetaMapper = new EmailInfoMetaMapper();

    }

    /**
     * Get all items/rows of this entity
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->EmailInfoMapper->get();
    }

    /**
     * GetById
     *
     * @param int|string $id Id of the entity to be fetched
     * 
     * @return EmailInfoEntity
     */
    public function getById($id)
    {
        
        return $this->EmailInfoMapper->get($id);
    }
    
    /**
     * Create new entity
     *
     * @param $payload array
     * 
     * @return EmailInfoEntity Newly created entity
     */
    public function create(array $payload = [])
    {
        //separate the passed data to entityData and metaData
        $keys = $this->separateEntityAndMeta($payload);

        //create instance of EmailInfoEntity with the passed data
        $emailInfo = EmailInfoEntity::makeInstance($keys['entityData']);
        
        //get the id of the saved EmailInfoEntity
        $insertionId = $this->EmailInfoMapper->add($emailInfo);


        //check if there is any insertion id
        if(!$insertionId){
            //no insertion id found, it means the add operation has failed
            return false;
        }

        //check if the metaData is present in the passed emailInfo data
        if(!empty($keys['metaData'])){
            //metaData is present, so add the meta data for the particular emailInfo

            //iterate over all the meta data and persist the meta data
            foreach($keys['metaData'] as $metaKey => $metaValue){

                //payload for creating the EmailInfoMetaEntity
                $payload = [
                    EmailInfoMetaKeys::META_KEY => $metaKey,
                    EmailInfoMetaKeys::EMAIL_INFO_ID => $insertionId,
                    EmailInfoMetaKeys::META_VALUE => $metaValue
                ];

                //create instance of EmailInfoMetaEntity with the payload
                $metaEntity = EmailInfoMetaEntity::makeInstance($payload);

                //persist the EmailInfoMetaEntity
                $this->EmailInfoMetaMapper->add($metaEntity);

            }
        }

        $EmailInfo = $this->EmailInfoMapper->get($insertionId);

        //return the EmailInfoEntity
        return $EmailInfo;
    }

    /**
     * Update the entity by id
     *
     * @param string|int $id Id of the entity to be updated
     * @param array  $payload Data to be updated for the entity
     * 
     * @return bool updated entity status
     */
    public function update($id, array $payload, bool $canAddNew = false)
    {

        //separate the entity and meta keys
        $keys = $this->separateEntityAndMeta($payload);

        $entity = $this->EmailInfoMapper->get($id);

        if(!$entity){

            return false;
        }

        //map the entityData to the EmailInfoEntity
        foreach($keys['entityData'] as $key => $value){
            $this->mapKeyToEntity($entity, $key, $value);
        }

        //persist the updated message
        $success = $this->EmailInfoMapper->update($entity);

        //check if meta data is avaialble
        if(!empty($keys['metaData'])){

            //iterate over all the metaData passed as payload
            foreach($keys['metaData'] as $key => $value){
                
                //check if the metakey is valid meta key
                if(isset($entity->metaData[$key])){
                    //key is valid meta key, so update the metaData

                    //create instance of the metaEntity with the metaId
                    $metaEntity = $this->EmailInfoMetaMapper->get($entity, $entity->metaData[$key]['id']);
                    //assign new meta value
                    $metaEntity->metaValue = $value;

                    //persist the updated meta values
                    $this->EmailInfoMetaMapper->update($metaEntity);

                }
                else
                {
                    //key does not exists

                    //check if new key can be added
                    if($canAddNew){
                        //create new meta entry 

                        //payload for new metaEntity creation
                        $payload = [
                            EmailInfoMetaKeys::META_KEY => $key,
                            EmailInfoMetaKeys::EMAIL_INFO_ID => $entity->id,
                            EmailInfoMetaKeys::META_VALUE => $value
                        ];
                        //creating instance of EmailInfoMetaEntity
                        $metaEntity = EmailInfoMetaEntity::makeInstance($payload);

                        //persist the metaEntity
                        $this->EmailInfoMetaMapper->add($metaEntity);

                    }
                }

            }
        }

        return $success;
    }

    /**
     * Delete entity by id
     *
     * @param string $id The entity to be deleted
     * 
     * @return bool Deleted entity status
     */
    public function delete( $id): bool
    {
        $entity = $this->EmailInfoMapper->get($id);
        
        if(!$entity){
            return false;
        }

        return $this->EmailInfoMapper->delete($entity);
    }


    /**
     * This is a helper function to separate the entity and meta keys for EmailInfo
     * 
     * @param $data array
     * 
     * @return array
     * 
     */
    private function separateEntityAndMeta(array $data = [])
    {

        //empty arrays for storing entity and meta data
        $entityData = [];
        $metaData = [];

        //iterating over keys to separate the entity and meta data
        foreach($data as $key => $value)
        {
            //Check if key is in the allowedEntityValues array
            if(in_array($key, $this->allowedEntityValues))
            {
                //key is in the allowedEntityValues array, so it is enity key
                $entityData[$key] = $value;
            }
            else
            {
                //key is not in the allowedEntityValues array, so it is meta key
                $metaData[$key] = $value;

            }
        }
        //return the entityData and metaData
        return [
            'entityData' => $entityData,
            'metaData' => $metaData
        ];

    }


 
    /**
     * This fucntion is used to map the keys to the EmailInfoEntity
     * 
     * @param $entity EmailInfoEntity
     * @param $key string|int
     * @param $value string|int
     * 
     * @return void
     * 
     */
    private function mapKeyToEntity(EmailInfoEntity $entity, string $key, $value){
        
        switch($key){

            case EmailInfoKeys::ID:
                $entity->id = $value;
                break;

            case EmailInfoKeys::PAYLOAD:
                $entity->payload = $value;
                break;

            case EmailInfoKeys::IS_PROCESSED:
                $entity->isProcessed = $value;
                break;

            case EmailInfoKeys::FAIL_COUNT:
                $entity->failCount = $value;
                break;
            case EmailInfoKeys::CREATED_AT:
                $entity->createdAt = $value;
                break;
            case EmailInfoKeys::UPDATED_AT:
                $entity->updatedAt = $value;
                break;
            case EmailInfoKeys::IS_DELETED:
                $entity->isDeleted = $value;
                break;
            case EmailInfoKeys::TENANT:
                $entity->payload = $value;
                break;
        }
    }

}
<?php
/**
 * This file contains implementation class for EmailInfoMapper
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\EmailInfoEntity;
use App\Keys\EmailInfo\EmailInfoKeys;
use App\Keys\EmailInfo\EmailInfoMetaKeys;
use App\Models\EmailInfoMetaModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\EmailInfoModel;
use Illuminate\Support\Facades\DB;

//This class implements IEmailInfoMapper Interface
class EmailInfoMapper implements IEmailInfoMapper{

    /**
     * 
     * This method persists EmailInfoEntity to the database
     * 
     * @param EmailInfoEntity $entity
     * 
     * @return boolean 
     * 
     */
    public function add(EmailInfoEntity $entity){

        //create instance of EmailInfoModel
        $model = new EmailInfoModel();

        $model = $this->mapEmailInfoEntityToModel($entity, $model);

        //persist data to database
        $success = $model->save();

        //if save operation is successful, then get the insertion id
        if($success){
            //assign the insertion id to success variable
            $success = $model->id;
        }

        //returning the boolean for save operation
        return $success;
        
    }

    /**
     * 
     * This method deletes EmailInfoEntity from the database
     * 
     * @param EmailInfoEntity $entity
     * 
     * @return boolean 
     * 
     */
    public function delete(EmailInfoEntity $entity){
        try{
            //fetch the message from the database
            $model = EmailInfoModel::findOrFail($entity->id);
        }
        catch(ModelNotFoundException $e){
            return false;
        }

        $model->is_deleted = 1;
        $success = $model->save();

        return $success;

    }


    /**
     * This method retrieves one or all message data from persistance storage
     * 
     * @param $id int|string
     * 
     * @return array|EmailInfoEntity
     * 
     */
    public function get($id = null){

        //if parent id is provided, then fetch only that data
        if($id){
            try{
                //request data from persistance storage
                $model = EmailInfoModel::findOrFail($id);
            }
            catch(ModelNotFoundException $e){
                //data not found for the passed id
                return false;
            }

          
            //data is found, creating an instance of the EmailInfoEntity with the fetched data
            $entity = EmailInfoEntity::makeInstance($model->toArray());

            $metaEntities = (new EmailInfoMetaMapper)->get($entity);

            foreach($metaEntities as $metaEntity){

                //storing the meta data
                $entity->metaData[$metaEntity->metaKey] = [
                    'id'=> $metaEntity->id,
                    'value'=>$metaEntity->metaValue,
                ];
            }
            return $entity;
        }
        else{

            
            $models = EmailInfoModel::where('is_deleted', '0')->get()->toArray();

            $entities = [];

            foreach ($models as $model) {

                $entity = EmailInfoEntity::makeInstance($model);

                // $entity = $this->getMetaData($entity);

                $entities[] = $entity;

            }

            if(empty($entities)){
                return [];
            }
    
            $entities = $this->getAllEntityMeta($entities);
    

            return $entities;
           
        }

    }

    public function getByColumn($columnName,$columnValue) {

        $models = EmailInfoModel::where($columnName,$columnValue)->where(EmailInfoKeys::IS_DELETED, 0)->get()->toArray();

        $entities = [];

        foreach ($models as $model) {

            $entity = EmailInfoEntity::makeInstance($model);

            // $entity = $this->getMetaData($entity);

            $entities[] = $entity;

        }

        if(empty($entities)){
            return [];
        }

        $entities = $this->getAllEntityMeta($entities);

        return $entities;
    }

    /**
     * This method get's experts by custom sql
     * 
     * @param string $sql
     * 
     * @return bool|array EmailInfoEntity
     * 
     */
    public function getBySql($sql)
    {
        $result = DB::select($sql);

        $entities = [];

        foreach($result as $singleResult){

            $entity = EmailInfoEntity::makeInstance((array)$singleResult);

            // $entity = $this->getMetaData($entity);

            $entities[] = $entity;
        }

        if(empty($entities)){
            return [];
        }

        $entities = $this->getAllEntityMeta($entities);

        return $entities;
    }

    /**
     * This method updates EmailInfoEntity already present in database
     * 
     * @param EmailInfoEntity $entity
     * 
     */
    public function update(EmailInfoEntity $entity){

        try{
            //get the data from the database
            $model = EmailInfoModel::findOrFail($entity->id);
        }
        catch(ModelNotFoundException $e){
            return false;
        }

        $model = $this->mapEmailInfoToModel($entity, $model);

        //persist updated message data to database
        $success = $model->save();

        //return the boolean for the success
        return $success;

    }

    /**
     * This is helper function to fetch meta data for EmailInfo
     * 
     * @param EmailInfoEntity $entity object
     * 
     * @return EmailInfoEntity
     * 
     */
    private function getMetaData(EmailInfoEntity $entity)
    {

        $metaMapper = new EmailInfoMetaMapper();

        $metaEntities = $metaMapper->get($entity);

        $metaDataArray = [];

        foreach ($metaEntities as $metaEntity) {

            //creating key values for the meta data in metaDataArray
            //like ['metaKey' => ['id' => '--id--', 'value' => '--value--']]
            $metaDataArray[$metaEntity->metaKey] = [
                'id' => $metaEntity->id,
                'value' => $metaEntity->metaValue
            ];
        }

        $entity->metaData = $metaDataArray;

        return $entity;
    }

    private function getAllEntityMeta(array $emailInfoEntities){
        $idsToFetchMeta = [];
  
        foreach($emailInfoEntities as $emailInfoEntity){
          $idsToFetchMeta[] = $emailInfoEntity->id;
        }
  
        $allEmailInfoMeta = EmailInfoMetaModel::whereIn(EmailInfoMetaKeys::EMAIL_INFO_ID, $idsToFetchMeta)->get()->toArray();
  
        $groupedEmailInfoMeta = [];
  
        foreach($allEmailInfoMeta as $eachMeta){
          if(!isset($groupedEmailInfoMeta[$eachMeta[EmailInfoMetaKeys::EMAIL_INFO_ID]])){
            $groupedEmailInfoMeta[$eachMeta[EmailInfoMetaKeys::EMAIL_INFO_ID]] = [];
          }
          
          $groupedEmailInfoMeta[$eachMeta[EmailInfoMetaKeys::EMAIL_INFO_ID]][] = $eachMeta;
  
        }
        
        foreach($emailInfoEntities as $emailInfoEntity){
          if(isset($groupedEmailInfoMeta[$emailInfoEntity->id])){
              foreach($groupedEmailInfoMeta[$emailInfoEntity->id] as $groupedEmailInfoMeta){
              $emailInfoEntity->metaData[$groupedEmailInfoMeta[EmailInfoMetaKeys::META_KEY]] = [
                  'id' => $groupedEmailInfoMeta[EmailInfoMetaKeys::ID],
                  'value' => $groupedEmailInfoMeta[EmailInfoMetaKeys::META_VALUE]
              ];
              }
          }// end of isset
        }
  
        return $emailInfoEntities;
  
    }


    /**
     * 
     * This method maps a EmailInfoEntity to the EmailInfoModel
     * 
     * @param EmailInfoEntity $entity
     * @param EmailInfoModel $model
     * 
     * @return EmailInfoModel
     * 
     */
    private function mapEmailInfoToModel(EmailInfoEntity $entity, EmailInfoModel $model){
        
        $model->id = $entity->id;
        $model->payload = $entity->payload;
        $model->is_processed = $entity->isProcessed;
        $model->fail_count = $entity->failCount;
        $model->created_at = $entity->createdAt;
        $model->updated_at = $entity->updatedAt;
        $model->is_deleted = $entity->isDeleted;
        
        return $model;
    }

}
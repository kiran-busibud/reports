<?php

namespace App\Data\Services\Attachment;

use App\Data\Entities\Attachment\AttachmentEntity;
use App\Data\Keys\Attachment\AttachmentKeys;
use App\Data\Repositories\Attachment\AttachmentRepository;
use App\Data\CommunicationChannel\Sendgrid\Services\AttachmentHandler;
use App\Data\Keys\Attachment\AttachmentMetaKeys;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AttachmentService
{

    protected $attachmentRepository;

    protected $attachmentHandler;

    protected $disk;

    public function __construct() {

        $this->attachmentRepository = new AttachmentRepository();

        $this->attachmentHandler = new AttachmentHandler() ;

        $this->disk = Storage::disk();

    }

    public function downloadAttachment(array $attachment, $onlyPublic = false){

        if(empty($attachment)){
            return false;
        }

        if(isset($attachment[AttachmentKeys::ID])){

            $attachmentData = $this->attachmentRepository->getById($attachment[AttachmentKeys::ID]);
            
            if(!$attachmentData){
                return false;
            }

            if(isset($attachmentData->metaData[AttachmentMetaKeys::IS_DIRECT_LINK]['value'])){
                if($attachmentData->metaData[AttachmentMetaKeys::IS_DIRECT_LINK]['value'] == '1'){
                    return response()->streamDownload(function() use ($attachmentData){
                        echo file_get_contents($attachmentData->attachmentUrl);
                    });
                }
            }
            $filePath = $attachmentData->attachmentUrl;
            // $filePath = $attachmentData->attachmentUrl;
        
        }else{

            //checking if attachment exists
            $attachmentData = $this->attachmentRepository->getByAttachmentName($attachment[AttachmentKeys::ATTACHMENT_NAME]);

            if(!$attachmentData){
                return false;
            }

            // $filePath = $attachmentData->attachmentUrl;

        }

        if($onlyPublic){
            if(!(isset($attachmentData->metaData[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT]) && $attachmentData->metaData[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT]["value"] == 1)){
                return false;
            }
        }

        $filePath = $attachmentData->attachmentUrl;

        try{
            return response()->file(storage_path('/app/public'.$filePath));
        }
        catch(Exception $e){
            return false;
        }

    }


    /**
     * Add single or multiple attachment
     * 
     * @param array $attachment
     * @param $files
     */
    public function addAttachment(array $attachment, $allFiles, $attachmentInfo = null, $parsing = false, $text = "")
    {

        if($attachmentInfo){
            $attachmentInfo = json_decode($attachmentInfo, true);
        }

        //if files empty
        if(empty($allFiles)){
            return false;
        }


        Log::debug("ALL_FILES : ".print_r($allFiles, true));

        //unique id for batch number
        $batchNumber = Str::uuid()->toString();

        $batchAttachmentSize = 0;

        if(isset($attachment[AttachmentKeys::BATCH_NUMBER]) && !empty($attachment[AttachmentKeys::BATCH_NUMBER])){

            $batchNumber = $attachment[AttachmentKeys::BATCH_NUMBER];

            //checking if batchNumber already exists
            $attachment = $this->attachmentRepository->getByBatchNumber($batchNumber);
            
            //if batch number not exists
            if(!$attachment){
                return false;
            }

            // //getting batch files size
            // foreach($attachment as $attachmentEntity){
                
            //     $batchAttachmentSize += intval($attachmentEntity->attachmentSize);
            // }
        }


        // //checking if file size is valid
        // if(!$this->attachmentHandler->isAttachmentSizeValid($allFiles, $batchAttachmentSize)){
        //     return false;
        // }

        //uploading attachments
        $filesList = $this->attachmentHandler->moveAllAttachment($allFiles, $parsing);

        //status for multiple files
        $uploadedStatus = [];


        $embedded = 0;
        $contentId = null;

        if(isset($attachment["dropped"]) && $attachment["dropped"] === true){

            $embedded = 1;
            $contentId = Str::uuid()->toString();
        }

        Log::debug(print_r($attachmentInfo, true));

        foreach($filesList as $file){

            if(!isset($attachment["dropped"])){

                $embedded = 0;
                $contentId = null;

            }

            
            Log::debug(print_r($file, true));

            if($attachmentInfo){

                Log::debug("Attachment INFO Present");

                if(isset($attachmentInfo[$file["id"]])){

                    Log::debug("Attachment INFO file found");

                    $singleAttachmentInfo = $attachmentInfo[$file["id"]];

                    Log::debug("Attachment Info ".print_r($attachmentInfo[$file["id"]], true));

                    if(isset($singleAttachmentInfo['content-id'])){

                        Log::debug("Attachment INFO Single Attachment found");
                        
                        $contentId = $singleAttachmentInfo['content-id'];

                        if($parsing){

                            if(str_contains($text, "cid:$contentId")){
                                $embedded = 1;
                            }

                        }


                    }

                }

            }
            
            //attachment data
            $attachmentData = [];
 
            $attachmentData[AttachmentKeys::ATTACHMENT_URL] = $file[AttachmentKeys::ATTACHMENT_URL];
            // $attachmentData[AttachmentKeys::ATTACHMENT_URL] = $file[AttachmentKeys::ATTACHMENT_URL];
            $attachmentData[AttachmentKeys::BATCH_NUMBER] = $batchNumber;
            $attachmentData[AttachmentKeys::ORIGINAL_NAME] = $file[AttachmentKeys::ORIGINAL_NAME];
            $attachmentData[AttachmentKeys::ATTACHMENT_NAME] = $file[AttachmentKeys::ATTACHMENT_NAME];
            $attachmentData[AttachmentKeys::ATTACHMENT_SIZE] = $file[AttachmentKeys::ATTACHMENT_SIZE];
            $attachmentData[AttachmentKeys::ATTACHMENT_TYPE] = $file[AttachmentKeys::ATTACHMENT_TYPE];
            $attachmentData[AttachmentKeys::EMBEDDED] = $embedded;
            $attachmentData[AttachmentKeys::CONTENT_ID] = $contentId;
            $attachmentData[AttachmentKeys::ATTACHMENT_EXTENSION] = $file[AttachmentKeys::ATTACHMENT_EXTENSION];
            // $attachmentData[AttachmentKeys::UPLOADED_DATE] = Carbon::now()->toDateTimeString();
            // $attachmentData[AttachmentKeys::UPLOADED_DATE_GMT] = Carbon::now('GMT')->toDateTimeString();
            $attachmentData[AttachmentKeys::DELETED] = 0;

            if(isset($attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT])){
                $attachmentData[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT] = $attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT];
            }

            //storing in db
            $status = $this->attachmentRepository->create($attachmentData);

            $isPublic = false;

            if(isset($attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT]) && $attachment[AttachmentMetaKeys::IS_PUBLIC_ATTACHMENT] == 1){
                $isPublic = true;
            }

            if(!$status){
                //if failed to add in db

                $payload = [
                    // AttachmentKeys::ATTACHMENT_URL=> config('app.url').$file[AttachmentKeys::ATTACHMENT_URL],
                    'attachmentName'=>$file[AttachmentKeys::ATTACHMENT_NAME],
                    'batchNumber'=>$batchNumber,
                    'status'=>false,
                ];

                if($isPublic){
                    $payload["publicURL"] = null;
                }

                $uploadedStatus[] = $payload;

            }else{

                $payload = [
                    'attachmentId' => $status->id,
                    // AttachmentKeys::ATTACHMENT_URL=> config('app.url').($status->attachmentUrl),
                    'attachmentName'=>$status->attachmentName,
                    'batchNumber'=>$status->batchNumber,
                    'contentId' => $contentId,
                    'status'=>true,
                ];
                
                if($isPublic){
                    $payload["publicURL"] = url(config('app.attachmentPathUnauthorized').'/'.$status->id);
                }

                $uploadedStatus[] = $payload;
            }
            
        }

        return $uploadedStatus;

    }


  public function addCdnAttachments($attachments)
  {
    $batchNumber = Str::uuid()->toString();
    $uploadedStatus = [];

    $attachmentRepository = new AttachmentRepository();

    foreach($attachments as $file){
            
      //attachment data
      $attachmentData = [];
      $attachmentData[AttachmentKeys::ATTACHMENT_URL] = $file['url'];
      $attachmentData[AttachmentKeys::BATCH_NUMBER] = $batchNumber;
      $attachmentData[AttachmentKeys::ORIGINAL_NAME] = $file['originalFileName'];
      $attachmentData[AttachmentKeys::ATTACHMENT_NAME] = $file['name'];
      $attachmentData[AttachmentKeys::ATTACHMENT_SIZE] = $file['size'];
      $attachmentData[AttachmentKeys::ATTACHMENT_TYPE] = $file['mimeType'];
      $attachmentData[AttachmentKeys::ATTACHMENT_EXTENSION] = $file['extension'];
      $attachmentData[AttachmentKeys::EMBEDDED] = 0;
      $attachmentData[AttachmentKeys::CONTENT_ID] = null;
      $attachmentData[AttachmentMetaKeys::IS_DIRECT_LINK] = 1;
      $attachmentData[AttachmentKeys::DELETED] = 0;

      //storing in db
      $status = $attachmentRepository->create($attachmentData);

      if(!$status){
          //if failed to add in db
          $uploadedStatus[] = [
              // AttachmentKeys::ATTACHMENT_URL=> config('app.url').$file[AttachmentKeys::ATTACHMENT_URL],
              'attachmentName'=>$file[AttachmentKeys::ATTACHMENT_NAME],
              'batchNumber'=>$batchNumber,
              'status'=>false,
          ];

      }else{
          $uploadedStatus[] = [
              'attachmentId' => $status->id,
              // AttachmentKeys::ATTACHMENT_URL=> config('app.url').($status->attachmentUrl),
              'attachmentName'=>$status->attachmentName,
              'batchNumber'=>$status->batchNumber,
              'status'=>true,
          ];
      }
    }
    return ['batchNumber'=>$batchNumber, 'attachments'=>$uploadedStatus];
  }

    /**
     * update an Attachments
     * 
     * @return bool|array
     */
    public function updateAttachment(array $attachment, $file = null)
    {

        //checking if attachment id exists
        if(!isset($attachment[AttachmentKeys::ID])){
            return false;
        }

        $attachmentData = $this->attachmentRepository->getById($attachment[AttachmentKeys::ID]);

        if(!$attachmentData){
            return false;
        }

        $payload = [];

        //entites that cannot be update
        $constantEntity = [
            AttachmentKeys::ID,
        ];

        foreach($attachment as $key=>$value){
            //checking if key in constant entity array
            if(!in_array($key, $constantEntity)){
                //adding data to payload
                $payload[$key] = $value;
            }
        }

        if(!empty($file)){

            $fileData = null;
            
            //uploading file
            $fileData = $this->attachmentHandler->moveAttachment($file);

            if(empty($fileData)){
                return false;
            }

            $fileData = $fileData[0];

            $payload[AttachmentKeys::ATTACHMENT_URL] = $fileData[AttachmentKeys::ATTACHMENT_URL];
            $payload[AttachmentKeys::ORIGINAL_NAME] = $fileData[AttachmentKeys::ORIGINAL_NAME];
            $payload[AttachmentKeys::ATTACHMENT_NAME] = $fileData[AttachmentKeys::ATTACHMENT_NAME];
            $payload[AttachmentKeys::ATTACHMENT_SIZE] = $fileData[AttachmentKeys::ATTACHMENT_SIZE];
            $payload[AttachmentKeys::ATTACHMENT_TYPE] = $fileData[AttachmentKeys::ATTACHMENT_TYPE];
            $payload[AttachmentKeys::ATTACHMENT_EXTENSION] = $fileData[AttachmentKeys::ATTACHMENT_EXTENSION];
            // $payload[AttachmentKeys::UPLOADED_DATE] = Carbon::now()->toDateTimeString();
            // $payload[AttachmentKeys::UPLOADED_DATE_GMT] = Carbon::now('GMT')->toDateTimeString();

        }

        if(empty($payload)){
            return false;
        }

        //storing in db
        $status = $this->attachmentRepository->update($attachment[AttachmentKeys::ID],$payload);

            //inserted status
        if(!$status){
            
            return false;
        }

        if(!empty($files)){
            
            return [
                // AttachmentKeys::ATTACHMENT_URL=>config('app.url').$payload[AttachmentKeys::ATTACHMENT_URL],
                AttachmentKeys::ATTACHMENT_NAME=> config('app.url').$payload[AttachmentKeys::ATTACHMENT_NAME],
                AttachmentKeys::BATCH_NUMBER=> $attachment[AttachmentKeys::BATCH_NUMBER] ?? $attachmentData->batchNumber ,
                'status'=>true,
            ];
        }

        return $status;

    }

    /**
     * Get an Attachment either by id or batch number
     * 
     * @param array
     * @return array
     */
    public function getAttachment(array $attachment){

        //checking if attachment id exists
        if(isset($attachment[AttachmentKeys::ID])){
            
            $attachmentData = $this->attachmentRepository->getById($attachment[AttachmentKeys::ID]);

            if(!$attachmentData){
                return false;
            }

            $data = $this->parseAttachmentEntityToArray($attachmentData);

            return $data[0];

        }else if(isset($attachment[AttachmentKeys::BATCH_NUMBER])){
            
            $attachmentData = $this->attachmentRepository->getByBatchNumber($attachment[AttachmentKeys::BATCH_NUMBER]);

            if(!$attachmentData){
                return false;
            }

            $data = $this->parseAttachmentEntityToArray($attachmentData);

            return $data;
        }
    }

    public function getAllAttachment($start=false, $limit=false){

        //fetching all the attachment data
        $attachmentData = $this->attachmentRepository->getAll($start, $limit);

        //if no data found
        if(!$attachmentData){
            return false;
        }

        //parsing attachment entity to array
        $data = $this->parseAttachmentEntityToArray($attachmentData);

        return $data;
    }

    public function generateSignedUrl(AttachmentEntity $attachment)
    {
        
        $url = URL::signedRoute('publicSignedAttachment', ['payload'=>base64_encode(json_encode(['attachmentId' => $attachment->id, 'batchNumber'=>$attachment->batchNumber]))]);
        
        if(config('app.attachment.development')){
            $ngrokUrl = config('app.attachment.url');

            $url = str_replace(url("/"),$ngrokUrl, $url);
        }
        return $url;
    }

     /**
     * Helper function for parsing AttachmentEntity to array
     * 
     * @param array|AttachmentEntity
     */
    private function parseAttachmentEntityToArray($attachment){

        //storing attachment data
        $attachmentEnitityList = [];

        if($attachment instanceof AttachmentEntity){
            $attachment = [$attachment];
        }

        foreach($attachment as $attachmentEntity){
            
            $attachmentEnitityList[] = [
       
                AttachmentKeys::ID => $attachmentEntity->id,
                AttachmentKeys::ATTACHMENT_URL => url(config('app.attachmentPath').'/'.$attachmentEntity->id),
                AttachmentKeys::BATCH_NUMBER => $attachmentEntity->batchNumber,
                AttachmentKeys::ORIGINAL_NAME => $attachmentEntity->originalName,
                AttachmentKeys::ATTACHMENT_NAME => $attachmentEntity->attachmentName,
                AttachmentKeys::ATTACHMENT_SIZE => $attachmentEntity->attachmentSize,
                AttachmentKeys::ATTACHMENT_TYPE => $attachmentEntity->attachmentType,
                AttachmentKeys::ATTACHMENT_EXTENSION => $attachmentEntity->attachmentExtension,
                AttachmentKeys::UPLOADED_DATE => $attachmentEntity->uploadedDate,
                AttachmentKeys::UPLOADED_DATE_GMT => $attachmentEntity->uploadedDateGmt,
                AttachmentKeys::DELETED => $attachmentEntity->deleted,
            ];
        }

        return $attachmentEnitityList;

    }

    public function deleteAttachment(array $attachment){
          
        //checking if attachment id exists
          if(isset($attachment[AttachmentKeys::ID])){
            
            //checking if attachment data is exists in record
            $attachmentData = $this->attachmentRepository->getById($attachment[AttachmentKeys::ID]);

            //if not data found or already deleted
            if(!$attachmentData || $attachmentData->deleted == 1){
                return false;
            }
            //deleting attachment from record
            $data = $this->attachmentRepository->delete($attachment[AttachmentKeys::ID]);
            
            //todo [need to check if file is already used]

            //deleting file from storage
            // $this->deleteFile($attachmentData->attachmentUrl);
           
            return $data;

        }else if(isset($attachment[AttachmentKeys::BATCH_NUMBER])){
            
            $attachmentData = $this->attachmentRepository->getByBatchNumber($attachment[AttachmentKeys::BATCH_NUMBER]);
            
            if(!$attachmentData){
                return false;
            }

            $data = $this->attachmentRepository->deleteByBatchNumber($attachment[AttachmentKeys::BATCH_NUMBER]);

            // foreach($attachmentData as $attachmentEntity){
            
                //deleting file from storage
                // $this->deleteFile($attachmentEntity->attachmentUrl);
            // }

            return $data;
        }
    }

    private function deleteFile($filePath){

        //checking if file exist
        if($this->disk->exists($filePath)){
            //deleting a file
            if($this->disk->delete($filePath)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function mapMultipleBatchNumberIntoOne(array $batchNumbers)
    {
        return $this->attachmentRepository->mapMultipleBatchNumberIntoOne($batchNumbers);
    }
 
}
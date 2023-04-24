<?php
/**
 * File contains interface for AttachmentMapper class
 * 
 */
namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\AttachmentEntity;

interface IAttachmentMapper{

    public function add(AttachmentEntity $attachment);
    public function get($id, $batchNumber, $start, $limit, $isAll);
    public function delete($id);
    public function update(AttachmentEntity $attachment, $id);
    public function getByColumnMultiple($columnName, $columnValue);

}
?>
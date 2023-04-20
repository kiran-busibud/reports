<?php

/**
 * This file contains IAttachmentMetaMapper specifications. AttachmentMetaMapper will implement this interface.
 * 
 * @author Yuvaraj N <yuvaraj@zinosi.com>
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\AttachmentMetaEntity;

interface IAttachmentMetaMapper
{
    public function add(AttachmentMetaEntity $metaEntity);
    public function delete(AttachmentMetaEntity $metaEntity);
    public function get($attachmentEntity, $metaId = false, array $attachmentIds= []);
    public function update(AttachmentMetaEntity $metaEntity);
}
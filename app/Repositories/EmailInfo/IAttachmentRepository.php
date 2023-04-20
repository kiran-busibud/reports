<?php

/**
 * This file contains base entity abstraction
 * 
 */

namespace App\Repositories\EmailInfo;

use App\Entities\EmailInfo\AttachmentEntity;

interface IAttachmentRepository
{
    
    public function createOrUpdate($id = null);
    public function getAll($start=false, $limit=false);
    public function getById(string $id);
    public function create(array $attachmentData = []);
    public function update(string $id, array $payload, bool $canAddNew = false);
    public function delete(string $id): bool;
    public function getByContentIds(array $contentIds);

}

?>
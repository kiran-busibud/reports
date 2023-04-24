<?php

/**
 * This file contains Inteface IEmailAttachmentMapper which every mapper will implement
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 * 
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\EmailAttachmentEntity;

interface IEmailAttachmentMapper{

    public function add(EmailAttachmentEntity $entity);
    public function delete(EmailAttachmentEntity $entity);
    public function get($id = null);
    public function update(EmailAttachmentEntity $entity);

}
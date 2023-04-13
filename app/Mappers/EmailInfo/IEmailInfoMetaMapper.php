<?php

/**
 * This file contains IEmailInfoMetaMapper specifications. 
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\EmailInfoEntity;
use App\Entities\EmailInfo\EmailInfoMetaEntity;

interface IEmailInfoMetaMapper
{
    public function add(EmailInfoMetaEntity $metaEntity);
    public function delete(EmailInfoMetaEntity $metaEntity);
    public function get(EmailInfoEntity $entity, $metaId = false);
    public function update(EmailInfoMetaEntity $metaEntity);
}
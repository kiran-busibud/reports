<?php

/**
 * This file contains Inteface IEmailInfoMapper which every mapper will implement
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 * 
 */

namespace App\Mappers\EmailInfo;

use App\Entities\EmailInfo\EmailInfoEntity;

interface IEmailInfoMapper{

    public function add(EmailInfoEntity $entity);
    public function delete(EmailInfoEntity $entity);
    public function get($id = null);
    public function update(EmailInfoEntity $entity);

}
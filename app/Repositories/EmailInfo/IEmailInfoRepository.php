<?php

/**
 * This file contains IEmailInfoRepository class
 * 
 * @author Maninderjit Singh <maninder@zinosi.com>
 */

namespace App\Repositories\EmailInfo;


interface IEmailInfoRepository
{

    public function getAll(): array;
    public function getById(string $id);
    public function create(array $payload = []);
    public function update(string $id, array $payload, bool $canAddNew = false);
    public function delete(string $id);

}
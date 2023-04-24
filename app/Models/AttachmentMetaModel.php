<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachmentMetaModel extends Model
{
    use HasFactory;
    protected $table = "hl_attachment_meta";
    public $timestamps = false;
    public $primaryKey = 'meta_id';
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachmentModel extends Model
{
    use HasFactory;
    protected $table = "hl_attachments";
    public $timestamps = false;
}
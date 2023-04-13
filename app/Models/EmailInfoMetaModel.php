<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailInfoMetaModel extends Model
{
    use HasFactory;

    protected $table = 'email_info_meta';
    protected $fillable = ['id','email_info_id','meta_key','meta_value'];
}

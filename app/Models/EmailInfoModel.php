<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailInfoModel extends Model
{
    use HasFactory;
    protected $table = 'email_info';
    protected $fillable = ['id','payload','is_processed', 'fail_count', 'created_at', 'updated_at','is_deleted','tenant'];
}

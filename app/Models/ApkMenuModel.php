<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApkMenuModel extends Model
{
    use HasFactory;

    protected $table = 'apk_menu';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'order',
        'status'
    ];
}

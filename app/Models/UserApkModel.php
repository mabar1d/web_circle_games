<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UserApkModel extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'email',
        'password',
        'token_jwt',
        'token_firebase'
    ];
    // protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        // //create event to happen on creating
        // self::creating(function ($model) {
        //     $model->created_by = Auth::id();
        //     // $model->m_game_uid = Str::orderedUuid()->getHex()->toString();
        // });

        // //create event to happen on creating
        // self::updated(function ($model) {
        //     $model->updated_by = Auth::id();
        // });
    }

    public function getCreatedAtAttribute() //to show created_at column
    {
        return Carbon::parse($this->attributes['created_at'])
            ->format('d M Y H:i');
    }

    public function getUpdatedAtAttribute() //to show updated_at column
    {
        return Carbon::parse($this->attributes['updated_at'])
            ->format('d M Y H:i');
    }
}

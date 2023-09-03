<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TournamentModel extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'm_tournament';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'id_created_by',
        'start_date',
        'end_date',
        'detail',
        'number_of_participants',
        'register_date_start',
        'register_date_end',
        'register_fee',
        'prize',
        'game_id',
        'type',
        'image',
        'terms_condition'
    ];
    protected $hidden = array('created_at', 'updated_at');

    protected static function boot()
    {
        parent::boot();

        //create event to happen on creating
        self::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        //create event to happen on creating
        self::updated(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function getPrizeAttribute($value)
    {
        return floatval($value);
    }

    public function getRegisterFeeAttribute($value)
    {
        return floatval($value);
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

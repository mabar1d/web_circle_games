<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class NewsTagsModel extends Model
{
    use HasFactory;

    protected $table = 'news_with_tag';
    protected $primaryKey = 'id';
    protected $fillable = [
        'news_id',
        'news_tag_id'
    ];

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

    public function news()
    {
        return $this->belongsTo(NewsModel::class);
    }

    public function tags()
    {
        return $this->belongsTo(TagsModel::class);
    }
}

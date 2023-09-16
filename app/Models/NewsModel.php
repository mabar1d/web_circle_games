<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class NewsModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'news';
    protected $primaryKey = 'id';
    protected $fillable = [
        'news_category_id',
        'title',
        'slug',
        'content',
        'image',
        'status',
        'created_by',
        'updated_by'
    ];
    protected $dates = ['deleted_at'];

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

    public function newsCategory()
    {
        return $this->hasOne(NewsCategoryModel::class, 'id', 'news_category_id');
    }

    public function newsTags()
    {
        return $this->hasMany(NewsTagsModel::class, 'news_id', 'id');
    }

    // public function newsTags()
    // {
    //     return $this->hasManyThrough(
    //         TagsModel::class,
    //         NewsTagsModel::class,
    //         'news_id',
    //         'id'
    //     );
    // }

    public function pivotNewsTags()
    {
        return $this->belongsToMany(
            TagsModel::class,
            NewsTagsModel::class,
            'news_id',
            'news_tag_id'
        );
    }
}

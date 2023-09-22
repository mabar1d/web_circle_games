<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VideoModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'video';
    protected $primaryKey = 'video_id';
    protected $fillable = [
        'title',
        'category_id',
        'slug',
        'content',
        'image',
        'link',
        'notify',
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

    public function category()
    {
        return $this->hasOne(NewsCategoryModel::class, 'id', 'category_id');
    }

    public function videoTag()
    {
        return $this->hasMany(VideoTagsModel::class, 'id', 'video_id');
    }

    public function pivotVideoTags()
    {
        return $this->belongsToMany(
            TagsModel::class,
            VideoTagsModel::class,
            'video_id',
            'tag_id'
        );
    }
}

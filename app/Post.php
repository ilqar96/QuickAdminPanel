<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Post extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    public $table = 'posts';

    protected $appends = [
        'image',
        'images',
    ];

    const ACTIVE_RADIO = [
        '1' => 'Active',
        '0' => 'Deactive',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'publish_date',
    ];

    protected $fillable = [
        'title',
        'active',
        'content',
        'created_at',
        'updated_at',
        'deleted_at',
        'category_id',
        'publish_date',
    ];

    public static function boot()
    {
        parent::boot();

        Post::observe(new \App\Observers\PostActionObserver);
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')->width(50)->height(50);
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('image')->last();

        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
        }

        return $file;
    }

    public function getImagesAttribute()
    {
        $files = $this->getMedia('images');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
        });

        return $files;
    }

    public function getPublishDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setPublishDateAttribute($value)
    {
        $this->attributes['publish_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

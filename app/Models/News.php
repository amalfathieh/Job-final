<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
class News extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'created_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('News');
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function images() :MorphMany {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function files() :MorphMany {
        return $this->morphMany(File::class, 'fileable');
    }
}

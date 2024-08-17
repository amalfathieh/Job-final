<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Seeker extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded=[];

    protected $casts = [
        'skills' => 'array',
        'certificates' => 'array'
    ];
    public static function boot()
    {
        parent::boot();
        // Validate uniqueness of user_id when creating a new company
        static::creating(function ($seeker) {
            if (self::where('user_id', $seeker->user_id)->exists()) {
                throw new \Exception('User already has a seeker job.');
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('Seeker');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function applies() {
        return $this->hasMany(Apply::class);
    }
}

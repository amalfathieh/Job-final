<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Opportunity extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id',
        'title',
        'body',
        'location',
        'job_type',
        'work_place_type',
        'job_hours',
        'qualifications',
        'skills_req',
        'salary',
        'vacant'
    ];

    protected $casts = [
        'qualifications' => 'array',
        'skills_req' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('Opportunity');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function savedByUsers() {
        return $this->belongsToMany(User::class, 'saves');
    }

    public function applies(){
        return $this->hasMany(Apply::class);
    }

    public function images(): MorphMany{
        return $this->morphMany(Image::class, 'imageable');
    }

    public function files(): MorphMany{
        return $this->morphMany(File::class, 'fileable');
    }
}

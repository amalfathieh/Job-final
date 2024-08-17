<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Company extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'company_name',
        'domain',
        'location',
        'about',
    ];

    public static function boot()
    {
        parent::boot();
        // Validate uniqueness of user_id when creating a new company
        static::creating(function ($company) {
            if (self::where('user_id', $company->user_id)->exists()) {
                throw new \Exception('User already has a company.', 400);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('Company');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function opportunities() {
        return $this->hasMany(Opportunity::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

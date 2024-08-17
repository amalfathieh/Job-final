<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Apply extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'seeker_id',
        'opportunity_id',
        'company_id',
        'status'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('Apply');
    }

    public function seeker(){
        return $this->belongsTo(Seeker::class);
    }

    public function opportunity() {
        return $this->belongsTo(Opportunity::class);
    }

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}

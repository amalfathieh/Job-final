<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Save extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'user_id',
        'opportunity_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('Save');
    }

    public function oppourtunities() {
        return $this->belongsTo(Opportunity::class);
    }
}

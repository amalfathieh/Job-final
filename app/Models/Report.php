<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Carbon;

class Report extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'created_by',
        'user_id',
        'reason_id',
        'another_reason',
        'notes',
        'is_viewed'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->useLogName('Report');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function getCreatedAtAttribute($date)
    // {
    //     return Carbon::parse($date)->format('M-d-Y H:i A');
    // }

    // public function getUpdatedAtAttribute($date)
    // {
    //     return Carbon::parse($date)->format('M-d-Y H:i A');
    // }
}

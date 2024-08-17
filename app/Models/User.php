<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Cog\Contracts\Ban\Bannable as BannableInterface;
use Cog\Laravel\Ban\Traits\Bannable;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements BannableInterface
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Bannable, LogsActivity;

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'roles_name',
        'is_verified',
        'fcm_token',
        'google_id',
        'google_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'roles_name' => 'array'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->useLogName('User');
    }

    public function seeker()
    {
        return $this->hasOne(Seeker::class);
    }
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function chats(){
        return $this->belongsToMany(Chat::class,'chat_user_pivot','user_id','chat_id');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function followers(){
        return $this->belongsToMany(User::class,'followers' , 'follower_id','followee_id')->withTimestamps();
    }
    public function followings(){
        return $this->belongsToMany(User::class,'followers' , 'followee_id' , 'follower_id')->withTimestamps();
    }

    public function setUserNameAttribute($value) {
        return $this->attributes['user_name'] = strtolower($value);
    }

    public function setPasswordAttribute($value) {
        return $this->attributes['password'] = bcrypt($value);
    }

    public function savedOpportunities() {
        return $this->belongsToMany(Opportunity::class, 'saves');
    }

    public function deviceTokens(){
        return $this->hasMany(DeviceToken::class);
    }

    public function routeNotificationForFcm($notification = null)
    {
        return $this->deviceTokens->pluck('token')->toArray();
    }

    public function reportsCreatedByUser()
    {
        return $this->hasMany(Report::class, 'created_by');
    }

    public function reportsToUser()
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    public function news() {
        return $this->hasMany(News::class, 'created_by', 'id');
    }

    public function contactInfo()
    {
        return $this->hasOne(ContactInfo::class);
    }
}

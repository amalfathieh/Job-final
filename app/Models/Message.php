<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Message extends Model
{
    use HasFactory;
    protected $fillable =[
        'id',
        'chat_id',
        'sender_id',
        'message',
    ];
    public function sender(){
        return $this->belongsTo(User::class,'sender_id');
    }
    public function chat(){
        $this->belongsTo(Chat::class);
    }
}

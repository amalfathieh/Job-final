<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'linkedin',
        'gitHub',
        'website',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

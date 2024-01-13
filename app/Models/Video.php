<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'path',
        'user_id',
        'description',
        'image',
        'extension'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

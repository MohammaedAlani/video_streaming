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
        'image'
    ];

    
    public function getPathAttribute($value)
    {
        return Storage::url($value);
    }

    public function getImageAttribute($value)
    {
        return Storage::url($value);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    public $timestamps = true;
    public function likevideos()
    
{
    return $this->hasMany(LikeVideo::class, 'video_id');
}
public function cocreation()
{
    return $this->hasMany(cocreation::class, 'video_id');
}
}

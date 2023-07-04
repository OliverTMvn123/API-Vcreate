<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeVideo extends Model
{
    use HasFactory;
    protected $table = 'likevideos';
    public $timestamps = true;
    public function video()
{
    return $this->belongsTo(Video::class, 'video_id');
}
}

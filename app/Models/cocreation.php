<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class cocreation extends Model
{
    use HasFactory;
    protected $table = 'cocreations';
    public $timestamps = true;
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
}

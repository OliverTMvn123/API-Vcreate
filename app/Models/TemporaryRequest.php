<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryRequest extends Model
{
    use HasFactory;
    protected $table = 'temporary_requests';
    public $timestamps = true;
    
}

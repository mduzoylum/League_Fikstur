<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hasmatch extends Model
{
    use HasFactory;

    protected $table = "hasmatch";
    protected $guarded = [];

    public $timestamps=false;
}

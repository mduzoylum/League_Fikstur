<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $table = "result";
    protected $guarded = [];
    public $timestamps = false;

    public function team()
    {
        return $this->belongsTo('App\Models\Team');
    }
}

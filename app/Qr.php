<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qr extends Model
{
    protected $table = "qr";
    protected $fillable = [
        'coursename','teacherid','date'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homework';
    protected $fillable = [
        'coursename','content','teacherid','departid','subtime'
    ];
}

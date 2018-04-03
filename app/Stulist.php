<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stulist extends Model
{
    protected $table = "stulist";
    protected $fillable = [
        'coursename','name','stuid','teacherid','files'
    ];
}

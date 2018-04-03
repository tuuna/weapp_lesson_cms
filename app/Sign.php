<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sign extends Model
{
    protected $table = "sign";
    protected $fillable = [
        'name','stuid','status','date','coursename','teacherid'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable =[
        'name',
        'description',
    ];
    public function parameters(){
        return $this->hasMany(Parameter::class);
    }
    public function assets(){
        return $this->hasMany(Asset::class);
    }

}

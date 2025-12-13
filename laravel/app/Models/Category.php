<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['name', 'parent_id', 'description'];

    public function parent() {
        return $this->belongsTo(Category::class,'parent_id');
    }
    public function children() {
        return $this->hasMany(Category::class,'parent_id');
    }
    public function parameters() {
        return $this->hasMany(Parameter::class);
    }
    public function assets() {
        return $this->hasMany(Asset::class);
    }
}

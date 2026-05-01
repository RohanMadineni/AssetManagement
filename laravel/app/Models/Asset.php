<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    //
    protected $fillable = [
        'name',
        'brand',
        'category_id',
        'user_id',
        'status'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function attribute_values(){
        return $this->hasMany(Attribute_Value::class);
    }
}

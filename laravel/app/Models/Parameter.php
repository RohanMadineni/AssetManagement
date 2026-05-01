<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    //
    protected $fillable=[
        'name',
        'category_id',
        'data_type',
        'is_required',
    ];
    public function category(){
        return $this->belongTo(Category::class);
    }
    public function attribute_values()
    {
        return $this->hasMany(Attribute_value::class);
    }

  

}

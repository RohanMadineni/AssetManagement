<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parameter extends Model
{
    //
    use HasFactory;
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    //
    //protected $fillable = ['name', 'description'];
    protected $fillable = ['category_id', 'name', 'data_type', 'is_required'];
    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function attributeValues() {
        return $this->hasMany(AttributeValue::class);
    }
}

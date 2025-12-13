<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    //
    protected $fillable = [
        'category_id', 'name', 'brand', 'serial_number', 'purchase_date', 'manufacture_year', 'assigned_to_user_id', 'location', 'status'
    ];
    
    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function attributeValues() {
        return $this->hasMany(AttributeValue::class);
    }
    public function user() {
        return $this->belongsTo(User::class,'assigned_to_user_id');
    }
}

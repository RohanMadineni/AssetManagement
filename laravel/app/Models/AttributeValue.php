<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    //
    protected $fillable = ['asset_id', 'parameter_id', 'value'];
    public function asset() {
        return $this->belongsTo(Asset::class);
    }
    public function parameter() {
        return $this->belongsTo(Parameter::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute_value extends Model
{
    //
    protected $fillable = [
        'asset_id',
        'parameter_id',
        'value'
    ];

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AssetAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\ElasticsearchTestController;
class Asset extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'name',
        'brand',
        'category_id',
        'user_id',
        'status',
        'price',
        'Warranty',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function attribute_values(){
        return $this->hasMany(Attribute_value::class);
    }
    public function assignments(){
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)
                    ->whereNull('returned_at');
    }

    // Scope: assets currently assigned to a specific user
    public function scopeAssignedTo($query, $userId)
    {
        return $query->whereHas('currentAssignment', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}

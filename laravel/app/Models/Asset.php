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
        return $this->hasMany(Attribute_Value::class);
    }
    public function assignments(){
        return $this->hasMany(AssetAssignment::class);
    }
    // public function currentAssignment(){
    //     return $this->hasOne(AssetAssignment::class)
    //                 ->whereNull('returned_at');
    // }
    // public function scopeAssignedTo($userId)
    // {
    //     return $this->whereHas('currentAssignment', function ($q) use ($userId) {
    //         $q->where('user_id', $userId);
    //     });
    // }
    // Current active assignment only
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
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($asset) {
    //         app(ElasticsearchTestController::class)->indexAsset($asset);
    //     });

    //     static::updated(function ($asset) {
    //         app(ElasticsearchTestController::class)->indexAsset($asset);
    //     });

    //     static::deleted(function ($asset) {
    //         app(ElasticsearchTestController::class)->deleteAsset($asset->id);
    //     });
    // }
}

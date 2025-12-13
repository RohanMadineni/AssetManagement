<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parameter;
use App\Models\Category;
use App\Models\Asset;
use App\Models\User;
use PDO;

class AssetController extends Controller
{
    public function index(Request $request){
        $query = asset::query()->with(['category', 'attributeValues.parameter']);
        
        if($request->filled('category_id')){
            $query->where('category_id', $request->integer('category_id'));
        }

        if($request->filled('status')){
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('filter')) {
            $filters = (array) $request->filter;
            foreach ($filters as $name => $value) {
                $query->whereHas('attributeValues', function ($q) use ($name, $value) {
                    $q->whereHas('parameter', function ($q2) use ($name) {
                        $q2->where('name', $name);
                    })->where('value', $value);
                });
            }
        }

        $perPage = $request->get('per_page', 15);
        $assets = $query->paginate($perPage);
        return response()->json($assets);
    }

    public function store(){

    }

    public function show(){

    }

    public function update(){

    } 

    public function destroy(){

    } 
}

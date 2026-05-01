<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Attribute_value;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    //
    public function index(Request $request, $id){
        $assets = Asset::with('attribute_values.parameter')
            ->where('user_id',Auth::id())
            ->findorFail($id);
        return response()->json($assets);
    }
    
    public function show(Request $request){
        $query = Asset::with('attribute_values.parameter')
                ->where('user_id', Auth::id());
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
            // $query->where($query->category->name, $request->category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('parameter_id') && $request->has('value')) {
            $query->whereHas('attribute_values', function ($q) use ($request) {
                $q->where('parameter_id', $request->parameter_id)
                ->where('value', $request->value);
            });
        }

        return $query->paginate(10);
    }

    public function store(Request $request){
        try{$validated = $request->validate([
            'name'=>'required|string',
            'category_id'=>'required',
            'status'=>'required',
            // 'brand'=>'required'
        ]);
        // dd($request->all());
        $asset = Asset::create([
            'name'=>$validated['name'],
            // 'brand'=>$validated['brand'],
            'brand'=>'test',
            'category_id'=>$validated['category_id'],
            'user_id'=>Auth::id(),
            'status'=>$validated['status'],
            // 'user_id'=>1,
            // 'attributes'=>'array',
            
        ]);
         

        if ($request->has('attributes')) {
            foreach ($request->attributes as $parameterId => $value) {
                Attribute_value::create([
                    'asset_id' => $asset->id,
                    'parameter_id' => $parameterId,
                    'value' => $value
                ]);
            }
        }

        return response()->json($asset, 201);} catch (\Exception $e) {

        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
    }

    public function update(Request $request, $id){
        $asset = Asset::findorFail($id);
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required',
            'brand' => 'required'
        ]);
        $asset->update($validated);
        // $asset->update($request->only(['name', 'brand', 'status']));

        if($request->has('attributes')){
            foreach($request->attributes as $parameterID=>$value){
                Attribute_value::updateorCreate([
                    'asset_id' => $asset->id,
                    'parameter_id'=> $parameterID,
                    'value' => $value,
                ]);
            }
        }

        return response()->json($asset);
    }

    public function destroy($id){
        $asset = Asset::findorFail($id);

        $asset->attribute_values()->delete();
        $asset->delete();

        return response()->json([
            'message' => 'Asset deleted'
        ]);
    }

    public function stats(){
        $user_id=Auth::id();
        // $assets = Asset::with('attribute_values.parameter')
        //     ->where('user_id',Auth::id());
        return response()->json([
            'total_assets' => Asset::where('user_id', $user_id)->count(),
            // 'assigned_assets' => Asset::whereNotNull('user_id')->count(),
            'assigned_assets' => Asset::where('user_id', $user_id)->where('status', 'assigned')->count(),
            'unassigned_assets' => Asset::where('user_id', $user_id)->where('status', 'available')->count(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Attribute_value;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        if($request->selected_user==0)
                $user_id = Auth::id();
        else $user_id = $request['selected_user'];
        $query = Asset::with('attribute_values.parameter')
                ->where('user_id', $user_id);
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
        // return response()->json($user_id);
        return $query->paginate(10);
    }
    // public function showAll(){
    //     $query = Asset::with('attribute_values.parameter');
    //     return $query->paginate(10);
    // }
    // public function showAll(Request $request){
    //     $query = Asset::with('attribute_values.parameter')
    //     ->where('assets.user_id', Auth::id())
    //     ->leftJoin('categories', 'assets.category_id', '=', 'categories.id')
    //     ->select('assets.*', 'categories.name as category_name');

    // if ($request->category_id) {
    //     $query->where('assets.category_id', $request->category_id);
    // }

    // if ($request->status) {
    //     $query->where('assets.status', $request->status);
    // }

    // if ($request->has('parameter_id') && $request->has('value')) {
    //     $query->whereHas('attribute_values', function ($q) use ($request) {
    //         $q->where('parameter_id', $request->parameter_id)
    //           ->where('value', $request->value);
    //     });
    // }

    // return response()->json($query->get());
    // }
    public function store(Request $request){
        try{$validated = $request->validate([
            'name'=>'required|string',
            'category_id'=>'required',
            'status'=>'required',
            'brand'=>'required',
            'warranty'=>'required',
            'price'=>'required',
            'selected_user'=>'required',
        ]);
        // dd($request->all());
        if($validated['selected_user']==0)
                $user_id = Auth::id();
        else $user_id=$validated['selected_user'];
        $asset = Asset::create([
            'name'=>$validated['name'],
            // 'brand'=>$validated['brand'],
            'brand'=>$validated['brand'],
            'category_id'=>$validated['category_id'],
            'Warranty'=>$validated['warranty'],
            'user_id'=>$user_id,
            'status'=>$validated['status'],
            'price'=>$validated['price'], 
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
            'brand' => 'required',
            'price' => 'required',
            'Warranty' => 'required',
        ]);
        $asset->update($validated);
        // $asset->update($request->only(['name', 'brand', 'status']));
        
        if($request->has('attributes')){
            foreach($request->input('attributes') as $parameterID=>$value){
               
                Attribute_value::updateorCreate(
                    [
                        'asset_id' => $asset->id,
                        'parameter_id'=> $parameterID
                    ],
                    [
                        'value' => $value
                    ]
                );
            }
        }

        return response()->json($request->input('attributes'));
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
        
        // return response()->json([
        //     'total_assets' => Asset::where('user_id', $user_id)->count(),
        //     'assigned_assets' => Asset::where('user_id', $user_id)->where('status', '=','assigned')->count(),
        //     'unassigned_assets' => Asset::where('user_id', $user_id)->where('status', '=', 'available')->count(),
        //     'under_maintenance' => Asset::where('user_id', $user_id)->where('status', '=', 'under maintenance')->count(),
        //     'no_categories' => Asset::where('user_id', $user_id)
        //                     ->select('category_id')
        //                     ->whereNotNull('category_id')
        //                     ->groupBy('category_id')
        //                     ->get()
        //                     ->count(),
        // ]);

        // return response()->json([
        //     'total_assets' => 2,
        //     'assigned_assets' => 5,
        //     'unassigned_assets' => 4,
        //     'under_maintenance' => 2,
        //     'no_categories' => 1,
        // ]);

        $assets = DB::select('select category_id, status, price from assets where user_id = ?', [$user_id]);  
        
        $total = count($assets);
        
        $contvalues = array_count_values(array_column($assets, 'status'));
        $sum = array_sum(array_column($assets, 'price'));
        $maintenance = 0;
        $unassigned = 0;
        $assigned = 0;
        if(array_key_exists('available', $contvalues))
            $unassigned = $contvalues['available'];
        if(array_key_exists('assigned', $contvalues))
            $assigned = $contvalues['assigned'];
        if(array_key_exists('under maintenance', $contvalues))
            $maintenance = $contvalues['under maintenance'];
        $categories = array_count_values(array_column($assets, 'category_id'));
        
        // $unassigned = $assets->where('status', '=','available')->count();
        // $assigned = $assets->where('status', '=','assigned')->count();
        // $maintenance = $assets->where('status', '=','under maintenance')->count();
       
        // $categories = $assets
        //               ->whereNotNull('category_id')
        //               ->groupBy('category_id')
        //               ->count();
        $cat_names = Category::pluck('name', 'id');
        return response()->json([
            'total_assets' => $total,
            'assigned_assets' => $assigned,
            'unassigned_assets' => $unassigned,
            'under_maintenance' => $maintenance,
            // 'no_categories' => count($categories),
            'catNames' => $cat_names,
            'cat_Array' =>$categories,
            'totalvalue' =>$sum,
        ]);

    }

    public function allstats(){
        $assets = DB::select('select category_id, status, price from assets');  
        
        $total = count($assets);
        $sum = array_sum(array_column($assets, 'price'));
        $contvalues = array_count_values(array_column($assets, 'status'));
        $maintenance = 0;
        $unassigned = 0;
        $assigned = 0;
        if(array_key_exists('available', $contvalues))
            $unassigned = $contvalues['available'];
        if(array_key_exists('assigned', $contvalues))
            $assigned = $contvalues['assigned'];
        if(array_key_exists('under maintenance', $contvalues))
            $maintenance = $contvalues['under maintenance'];
        $categories = array_count_values(array_column($assets, 'category_id'));

        $cat_names = Category::pluck('name', 'id');
        return response()->json([
            'total_assets' => $total,
            'assigned_assets' => $assigned,
            'unassigned_assets' => $unassigned,
            'under_maintenance' => $maintenance,
            // 'no_categories' => count($categories),
            'catNames' => $cat_names,
            'cat_Array' =>$categories,
            'totalvalue' =>$sum,
        ]);
    }

    public function upcomingWarranty(){
        // $user_id = Auth::id();
        $today = Carbon::today();
        $threshold = Carbon::today()->addDays(15);
        $assets = Asset::whereNotNull('Warranty')
                ->where('user_id',Auth::id())
                // ->where('Warranty', '>', $today)
                ->whereBetween('Warranty', [$today, $threshold])
                ->get()
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'model' => $asset->model,
                        'Warranty' => $asset->Warranty,
                        'status'=>$asset->status,
                        'brand'=>$asset->brand,
                        'days_left' => Carbon::today()->diffInDays($asset->Warranty, false),
                    ];
                });
        // return $assets->paginate(10);
        return response()->json($assets);
    }

    public function upcomingAllWarranty(){
        // $user_id = Auth::id();
        $today = Carbon::today();
        $threshold = Carbon::today()->addDays(15);
        $assets = Asset::whereNotNull('Warranty')
                // ->where('user_id',Auth::id())
                // ->where('Warranty', '>', $today)
                ->whereBetween('Warranty', [$today, $threshold])
                ->get()
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'model' => $asset->model,
                        'Warranty' => $asset->Warranty,
                        'status'=>$asset->status,
                        'brand'=>$asset->brand,
                        'days_left' => Carbon::today()->diffInDays($asset->Warranty, false),
                    ];
                });
        
        return response()->json($assets);
    }
} 

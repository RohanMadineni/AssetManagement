<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Attribute_value;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\SendRealtimeNotification;
use Illuminate\Support\Facades\Cache;
use App\Services\RabbitMQPublisher;

class AssetController extends Controller
{
    //
    public function index(Request $request, $id){
        $cacheKey = "asset." . Auth::id() . "." . $id;
        $assets = Cache::tags(['assets'])->remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return Asset::with('attribute_values.parameter')
                ->where('user_id',Auth::id())
                ->findorFail($id);
        });
        return response()->json($assets);

    }
    
    public function show(Request $request)
{
    $user_id = $request->selected_user == 0
        ? Auth::id()
        : $request->selected_user;

    $page = $request->get('page', 1);

    $cacheKey = "OwnedAssets:" . $user_id . ":" . md5(json_encode([
        'category_id' => $request->category_id,
        'status' => $request->status,
        'name' => $request->name,
        'parameter_id' => $request->parameter_id,
        'value' => $request->value,
        'page' => $page,
    ]));

    return Cache::tags(['assets'])->remember($cacheKey, now()->addMinutes(10), function () use ($user_id, $request) {

        $query = Asset::with('attribute_values.parameter', 'currentAssignment.user')
            ->AssignedTo($user_id);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->name) {
            $query->where('name', $request->name);
        }

        if ($request->has('parameter_id') && $request->has('value')) {
            $query->whereHas('attribute_values', function ($q) use ($request) {
                $q->where('parameter_id', $request->parameter_id)
                  ->where('value', $request->value);
            });
        }

        return $query->paginate(10);
    });
}
    public function showAll(Request $request)
{
    $page = $request->get('page', 1);

    $cacheKey = "AllAssets:" . md5(json_encode([
        'category_id' => $request->category_id,
        'status' => $request->status,
        'parameter_id' => $request->parameter_id,
        'value' => $request->value,
        'page' => $page,
    ]));

    return Cache::tags(['assets'])->remember($cacheKey, now()->addMinutes(10), function () use ($request) {

        $query = Asset::with('attribute_values.parameter', 'currentAssignment.user');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
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
    });
}
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
        
        if($validated['selected_user']==0)
                $user_id = Auth::id();
        else $user_id=$validated['selected_user'];
        $asset = Asset::create([
            'name'=>$validated['name'],
            'brand'=>$validated['brand'],
            'category_id'=>$validated['category_id'],
            'Warranty'=>$validated['warranty'],
            'user_id'=>$user_id,
            'status'=>$validated['status'],
            'price'=>$validated['price'], 
            
        ]);
         
        // event(new \App\Events\AssetCreated($asset));

        if ($request->has('attributes')) {
            foreach ($request->attributes as $parameter_id => $value) {
                Attribute_value::create([
                    'asset_id' => $asset->id,
                    'parameter_id' => $parameter_id,
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

        $asset->update([
            'name'=>$validated['name'],
            'brand'=>$validated['brand'],
            // 'category_id'=>$validated['category_id'],
            'Warranty'=>$validated['Warranty'],
            // 'user_id'=>$user_id,
            'status'=>$validated['status'],
            'price'=>$validated['price'], 
        ]);
        
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

    public function stats(Request $request){
        $user_id=Auth::id();

        $stats = Cache::tags(['assets'])->remember(
                    "dashboard.stats.$user_id", 
                    now()->addMinutes(5), 
                    function () use ($user_id){
                        $assets = Asset::with('currentAssignment')
                        ->assignedTo($user_id)
                        ->get();
                        $total = $assets->count();

                        $contvalues = $assets->countBy('status');
                        $sum = $assets->sum('price');

                        $unassigned = $contvalues['available'] ?? 0;
                        $assigned = $contvalues['assigned'] ?? 0;
                        $maintenance = $contvalues['under maintenance'] ?? 0;

                        $categories = $assets->countBy('category_id');

                        // $cat_names = Category::pluck('name', 'id');
                        $cat_names = Cache::remember(
                            'categories',
                            now()->addHour(),
                            fn() => Category::pluck('name', 'id')
                        );
                        return [
                            'total_assets' => $total,
                            'assigned_assets' => $assigned,
                            'unassigned_assets' => $unassigned,
                            'under_maintenance' => $maintenance,
                            'catNames' => $cat_names,
                            'cat_Array' =>$categories,
                            'totalvalue' =>$sum,
                        ];
                    }
                );
        return response()->json($stats);
    }

    public function allstats(){

        $allstats = Cache::tags(['assets'])->remember(
            "dashboard.allstats",
            now()->addMinutes(5),
            function() {
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

                // $cat_names = Category::pluck('name', 'id');
                $cat_names = Cache::remember(
                            'categories',
                            now()->addHour(),
                            fn() => Category::pluck('name', 'id')
                        );
                return [
                    'total_assets' => $total,
                    'assigned_assets' => $assigned,
                    'unassigned_assets' => $unassigned,
                    'under_maintenance' => $maintenance,
                    'catNames' => $cat_names,
                    'cat_Array' =>$categories,
                    'totalvalue' =>$sum,
                ];
            }
        );
        return response()->json($allstats);
    }

    public function upcomingWarranty(){
        $today = Carbon::today();
        $threshold = Carbon::today()->addDays(15);
        $user_id = Auth::id();
        $assets = Cache::tags(['assets'])->remember("warranty.$user_id", now()->addMinutes(10), function() use ($today, $threshold, $user_id){
                return Asset::whereNotNull('Warranty')
                ->AssignedTo($user_id)
                        // ->where('user_id',$user_id)
                        ->whereBetween('Warranty', [$today, $threshold])
                        ->paginate(5);
        });
        $assets->getCollection()->transform(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'model' => $asset->model,
                'Warranty' => $asset->Warranty,
                'status' => $asset->status,
                'brand' => $asset->brand,

                'days_left' => Carbon::today()
                    ->diffInDays($asset->Warranty, false),
            ];
        });

        return response()->json($assets);
    }

    public function upcomingAllWarranty(){
        $today = Carbon::today();
        $threshold = Carbon::today()->addDays(15);

        $assets = Cache::tags(['assets'])->remember("warranty.Upcomingall", now()->addMinutes(10), function() use ($today, $threshold){
                return Asset::whereNotNull('Warranty')
                    ->whereBetween('Warranty', [$today, $threshold])
                    ->paginate(5);
        });
        $assets->getCollection()->transform(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'model' => $asset->model,
                'Warranty' => $asset->Warranty,
                'status' => $asset->status,
                'brand' => $asset->brand,

                'days_left' => Carbon::today()
                    ->diffInDays($asset->Warranty, false),
            ];
        });
        
        return response()->json($assets);
    }

    public function assign(Request $request){
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'username' => 'required',
        ]);
        $alreadyAssigned = AssetAssignment::where('asset_id', $request->asset_id)
        ->whereNull('returned_at')
        ->exists();

        if ($alreadyAssigned) {
            return response()->json(['message' => 'Asset already assigned'], 400);
        }

        $assignment = AssetAssignment::create([
            'asset_id' => $request->asset_id,
            'user_id' => $request->user_id,
            'assigned_at' => now(),
        ]);
        
        // Optional: update asset status
        Asset::where('id', $request->asset_id)
            ->update(['status' => 'assigned']);

        Cache::tags(['assets'])->flush();
        app(RabbitMQPublisher::class)->publish(
            'asset.assigned',
            [
                'event' => 'asset.assigned',
                'asset_id' => $request->asset_id,
                'name' => $request->username,
                'user_id' => $assignment->user_id,
                'title' => 'Asset Assigned to',
                'message' => $request->username,
                'type' => 'success'
            ]
        );
        return response()->json($assignment);
    
    }

    public function returnAsset(Request $request){
        $request->validate([
            'asset_id'=> 'required',
        ]);

        $assignment = AssetAssignment::with('user')
            ->where('asset_id',$request->asset_id)
            ->whereNull('returned_at')
            ->first();

        if (!$assignment) {
            Asset::where('id', $request->asset_id)
            ->update(['status' => 'available']);
            return response()->json(['message' => 'Asset is not assigned'], 400);
        }

        $assignment->update([
            'returned_at' => now()
        ]);
        
        Asset::where('id', $request->asset_id)
            ->update(['status' => 'available']);
        
        Cache::tags(['assets'])->flush();
        app(RabbitMQPublisher::class)->publish(
            'asset.returned',
            [
                'event' => 'asset.returned',
                'asset_id' => $request->asset_id,
                // 'name' => $request->username,
                'user_id' => $assignment->user_id,
                'title' => "Asset {$assignment->asset_id}",
                'message' => 'returned',
                'type' => 'success'
            ]
        );
        return response()->json(['message' => 'Asset returned']);
    }

    public function history($id)
    {
        $history = AssetAssignment::with('user')
            ->where('asset_id', $id)
            ->orderByDesc('assigned_at')
            ->get();

        return response()->json($history);
    }

    public function recentlyAssigned(){
        $user_id = Auth::id();
        $today = Carbon::today()->addDays(1);
        $threshold = Carbon::today()->subDays(10);
        
        return Cache::tags(['assets'])->remember("recentlyAssiged.$user_id", now()->addMinutes(10), function() use ($today, $threshold, $user_id){
            $query = Asset::with('attribute_values.parameter', 'currentAssignment') 
            ->AssignedTo($user_id)
            ->whereHas('currentAssignment', function ($q) use ($today, $threshold) {
                        $q->whereBetween('assigned_at', [$threshold, $today]);
                    });
            return $query->paginate(5);
        });
        
    }

    public function recentlyAllAssigned(){
        $today = Carbon::today()->addDays(1);
        $threshold = Carbon::today()->subDays(13);
        
                    
        return Cache::tags(['assets'])->remember("recentlyAllAssiged", now()->addMinutes(10), function() use ($today, $threshold){
            $query = Asset::with(['attribute_values.parameter', 'currentAssignment'])
                    ->whereHas('currentAssignment', function ($q) use ($today, $threshold) {
                        $q->whereBetween('assigned_at', [$threshold, $today]);
                    });
            return $query->paginate(5);
        });
    }
    
    public function AssetHistory($id){
        $assignments = AssetAssignment::with('user')
        ->where('asset_id', $id);

        return $assignments->paginate(10);
    }

} 

<?php

namespace App\Http\Controllers;

use App\Services\ElasticsearchService;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetSearchController extends Controller
{
    //
    public function search(Request $request, ElasticsearchService $elastic)
    {   
        $query = $request->query('q');

        $results = $elastic->searchAssets($query);
        
        $ids = [];
        foreach($results['hits']['hits'] as $key => $value){
            $ids[] = $value['_id'];
        }
        // $ids = array_column($ids, '_id');

        // $assets = Asset::with('attribute_values.parameter')
        //         ->wherein('id', $results['hits']['hits']);
        // return $ids;
        $assets = Asset::with('attribute_values.parameter')
                ->wherein('id', $ids)
                ->get();
        return response()->json($assets);
        return response()->json($results['hits']['hits']);
    }
}

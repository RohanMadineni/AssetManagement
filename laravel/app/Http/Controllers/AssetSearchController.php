<?php

namespace App\Http\Controllers;

use App\Services\ElasticsearchService;
use Illuminate\Http\Request;

class AssetSearchController extends Controller
{
    //
    public function search(Request $request, ElasticsearchService $elastic)
    {   
        $query = $request->query('q');

        $results = $elastic->searchAssets($query);

        return response()->json($results['hits']['hits']);
    }
}

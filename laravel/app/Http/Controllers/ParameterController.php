<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Parameter;
use Illuminate\Http\Request;

class ParameterController extends Controller {
    public function index($id) {
        $category = Category::with('parameters')->findOrFail($id);
        return $category->parameters;
    }

    public function store(Request $request,$id) {
        $validated = $request->validate([
            'name'=>'required|string',
            'data_type'=>'required|in:string,number,boolean,date',
            'is_required'=>'boolean'
        ]);
        $category = Category::findOrFail($id);
        $param = $category->parameters()->create($validated);
        return response()->json($param,201);
    }

    public function update(Request $request,$id) {
        $param = Parameter::findOrFail($id);
        $validated = $request->validate([
            'name'=>'required|string',
            'data_type'=>'required|in:string, number, boolean, date',
            'is_required'=>'boolean',
            'default_value'=>'nullable|string'
        ]);
        $param->update($validated);
        return response()->json($param);
    }

    public function destroy($id) {
        $param = Parameter::findOrFail($id);
        $param->delete();
        return response()->json(['message'=>'Deleted']);
    }
}

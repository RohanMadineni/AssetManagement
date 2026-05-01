<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parameter;

class ParameterController extends Controller
{
    //
    public function index(Request $request, $id){
        $parameters = Parameter::where('category_id', $id)->get();
        return response()->json($parameters);
    }
    public function create(Request $request, $id){
        // print('Creating param');
        $validated = $request->validate([
            'name'=>'required|string|max:255',
            // 'name'=>'required',
            'data_type'=>'required',
            // 'data_type'=>'required|in:string, number, boolean, date',
            'is_required'=>'boolean',
        ]);
        $validated['category_id'] = $id;
        $parameter = Parameter::create($validated);

        return response()->json($parameter, 201);
    }
    public function update(Request $request, $id){
        $parameter = Parameter::findOrFail($id);

        $validated = $request->validate([
            // 'name' => 'sometimes|string|max:255',
            'name' => 'required',
            // 'data_type' => 'sometimes|in:string,number,boolean,date',
            'data_type' => 'required',
            'is_required' => 'boolean'
        ]);

        $parameter->update($validated);

        return response()->json($parameter);
    }
    
    public function destroy(Request $request, $id){
        $parameter = Parameter::findOrFail($id);

        if ($parameter->attributeValues()->exists()) {
            return response()->json([
                'message' => 'Cannot delete parameter with existing attribute values'
            ], 409);
        }

        $parameter->delete();

        return response()->json([
            'message' => 'Parameter deleted'
        ]);
    }
}

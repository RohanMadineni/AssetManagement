<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();
        return response()->json($categories);
    }
    
    public function name($id){
        $category = Category::findorFail($id);
        return response()->json($category);
    }
    public function create(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);    
    }
    
    public function update(Request $request, $id){

        $category = Category::findorFail($id);
        $validated = $request->validate([
            'name'=> 'sometimes|required|string|max:255',
            'description'=> 'nullable|string'
        ]);

        $category->update($validated);
        return response()->json($category);
    } 

    public function destroy($id){

        $category = Category::findorFail($id);

         if ($category->assets()->exists() || $category->parameters()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with existing assets or parameters.'
            ], 409);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}

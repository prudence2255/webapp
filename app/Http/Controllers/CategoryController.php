<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryPostsResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function __construct() {
    $this->middleware('auth:api');
  
}
     public $status = 200;
    public function index()
    {
        return response(['data' => Category::all()], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $validator = Validator::make($request->all(),
                     [
                     'name' => 'required|unique:categories',
                     ]   
                    );
         if ($validator->fails()) {
                        return response([
                        'errors' => $validator->errors(),
                        'status' => 'error'
                    ], 201);
                 }           
         $category = Category::create(['name' => $request->name]);
        return response([
            'message' => 'Category created successfully',
            'data' => $category,
            'status' => 'success',           
        ], $this->status);
         }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category_posts = $category->posts()->paginate(10);
        return response(['data' => $category, 'posts' => $category_posts]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(),[
            'name' => [
                'required',
                Rule::unique('categories')->ignore($category->id),
            ]
        ]);
        if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
                'status' => 'error',
            ], 201);
        }
        $category->update($request->all());
        return response([
        'message' => 'Category updated successfully',
        'data' => $category,
        'status' => 'success',
], $this->status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if($category->posts){
            $category->posts()->delete();
        }
        $category->delete();
        return response(['message' => 'Category deleted successfully'], $this->status);
    }
}

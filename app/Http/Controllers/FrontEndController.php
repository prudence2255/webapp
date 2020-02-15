<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Tag;
use App\Http\Resources\PostsResource;
use App\Http\Resources\HomePostsResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\CategoryResource;
use File;
use Storage;
use App\Http\Resources\CategoryPostsResource;
class FrontEndController extends Controller
{

  
    // get all post

    public function posts() {
        return PostsResource::collection(Post::where('published', 0)
                                        ->orderBy('created_at', 'DESC')
                                        ->paginate(8));
    }

    //get all categories
    
    public function categories() {
        return CategoryResource::collection(Category::all());
    }
// get each category posts
 public function category_posts(Category $category)
    {
        $category_posts = $category->posts()->where('published', 0)
                                            ->orderBy('created_at', 'DESC')
                                             ->paginate(8);
        return CategoryPostsResource::collection($category_posts);
    }

   //get all tags
   public function tags() {
       return response(['data' => Tag::all()]);
   } 
  
   public function tag_posts(Tag $tag) {

        $tag_posts = $tag->posts()->where('published', 0)
                                ->orderBy('created_at', 'DESC')
                                 ->paginate(8);
       return CategoryPostsResource::collection($tag_posts);
   }

   public function show_post(Post $post)
   {
       return response(['data' => new PostResource($post)]);
   }

   public function home_posts() {
       return HomePostsResource::collection(Category::all());
   }

   public function storeFiles() {
    $paths = public_path('storage/files');
    $files = File::files($paths);
    $urls = [];
    foreach ($files as $file => $value) {
   $path = substr($value, strpos($value, 'storage'));
   $url = url($path);
    array_push($urls, $url);
    }
    
    return response(['files' => $urls]);
  } 

   public function search(Request $request){
       $request->validate([
            'query' => 'required|min:3|string'
       ]);
       $query = $request->input('query');
    return PostsResource::collection(Post::where('post_title', 'LIKE', "%{$query}%")
                                    ->orderBy('created_at', 'DESC')
                                    ->paginate(8));
   }
}


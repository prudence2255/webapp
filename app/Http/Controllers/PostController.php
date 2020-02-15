<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Tag;
use App\Http\Resources\AdminPostResource;
use App\Http\Resources\AdminPostsResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Image;

class PostController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function __construct() {
         $this->middleware('auth:api')->except(['files']);
        
     }

    public function index()
    {
        if(auth()->user()->role === 'admin'){
           return AdminPostsResource::collection(Post::orderBy('created_at', 'DESC')
                                                            ->paginate(10));
        }else{
            return AdminPostsResource::collection(auth()->user()->posts()
                                                                ->orderBy('created_at', 'DESC')
                                                                 ->paginate(10));;
               
        }
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'post_title' => 'required|unique:posts',
            'post_content' => 'required',
            'post_img' => 'required|string',
            'img_alt' => 'required',
            'files' => 'nullable|string'
        ]);
        if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
                'status' => 'error',
            ], 201);
        }
       $post = Post::create([
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id,
            'post_title' => $request->post_title,
            'post_content' => $request->post_content,
            'source' => $request->source,
            'post_img' => $request->post_img,
            'tags' => $request->tags,
            'description' => $request->description,
            'published_at' => now(),
            'files' => is_array($request->upload) ? implode("|", $request->upload) :
                                                                 $request->upload,
            'img_alt' => $request->img_alt,
        ]);
        if($request->tags){
            $post->tags()->attach($request->tags);
       }
        return response([
                     'message' => 'Post created successfully',
                     'data' => new AdminPostsResource($post),
                     'status' => 'success',
                     ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response(['data' => new AdminPostResource($post)]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
      
        $data = $request->all();
        $validator = Validator::make($data, [
            'category_id' => 'required',
            'post_title' => [
                'required',
                Rule::unique('posts')->ignore($post->id),
            ],
            'post_content' => 'required',
            'files' => 'nullable|string'
        ]);
        if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
                'status' => 'error',
            ], 201);
        }
        if($request->tags){
            $post->tags()->sync($request->tags);
         }
       $post->update($data);
       return response([
           'message' => 'Post updated successfully',
           'data' => new AdminPostsResource($post),
           'status' => 'success',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete_image();
        $post->delete_file();
        $post->delete();
        return response(['message' => 'Post deleted successfully'], 201);
    }

    public function publish(Post $post){
        $post->published = true;
        $post->save();

        return response(['message' => 'Post published successfully'],201);
    }

   public function unpublish(Post $post) {
    $post->published = false;
    $post->save();
    return response(['message' => 'Post unpublished successfully'], 201);
   } 

   public function createThumbnail($requestPath, $path, $width, $height){
    $img = Image::make($requestPath)->resize($width, $height, function($constraint){
        $constraint->aspectRatio();
    });
    $img->save($path);
}  
public function files(Request $request){
    $file = $request->file;
    $fileFullName = $file->getClientOriginalName();
    $fileName = pathinfo($fileFullName, PATHINFO_FILENAME);
    $fileExt = $file->getClientOriginalExtension();
    $storedName = $fileName.'file'.time().'.'.$fileExt;
    $extensions = ['bmp','gif','jpeg','jpg','png'];
    $url = url('storage/posts/'.$storedName);
    if(in_array($fileExt, $extensions)){
        $filePath = public_path('storage/posts/'.$storedName);
        $this->createThumbnail(
            $file->getRealPath(), $filePath, 992, 500
        );
    }else{
        $request->file('file')->move(public_path("storage/posts"), $storedName);
    }
   return response(['url' => $url, 'name' => $fileName], 201);

}

public function post_image(Request $request) {
    $validator = Validator::make($request->all(),[
        'post_img' => 'required|mimes:bmp,gif,jpeg,jpg,png',
    ]);
   if($validator->fails()){
       return response([
           'errors' => $validator->errors(),
           'status' => 'error',
       ]);
   }
    $postImage = $request->post_img;
    $imageFullName = $postImage->getClientOriginalName();
    $imageName = pathinfo($imageFullName, PATHINFO_FILENAME);
    $imageExt = $postImage->getClientOriginalExtension();
    $storeImage = $imageName.'_'.time().'.'.$imageExt;
    $thumbNail = $imageName.'_thumb_'.time().'.'.$imageExt;
    $thumbNailPath = public_path('storage/thumbnails/'.$thumbNail);
    $imagePath = public_path('storage/posts/'.$storeImage);
    $this->createThumbnail(
        $postImage->getRealPath(), $thumbNailPath, 500, 250
    );
    $this->createThumbnail(
        $postImage->getRealPath(), $imagePath, 992, 500
    );

    $url1 = url('storage/posts/'.$storeImage);
    $url2 = url('storage/thumbnails/'.$thumbNail);
    $urls = [$url1, $url2];

    return response([
        'data' => $urls,
         'status' => 'success',
         'message' => 'Image uploaded successfully',
        ], 201);
}
}

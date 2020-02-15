<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomePostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
// public function title($title) {
//     if(strlen($title) > 80){
//      $title = substr($title, 0, strrpos(substr($title,0, 80), ' ')).'...';
//     }else{
//         return $title;
//     }

//     return $title;
// }

    public function img($image){
        if(strpos($image, "|") !== false){
            $url = explode("|", $image);
            return ['image' => $url[0], 'thumb' => $url[1]];
        }
       return $image;
    }
    public function post($posts){
      
        foreach ($posts as $i => $post) {
          $posts[$i] = [
            'id' => $post->id,
            'post_title' => $post->post_title,
            'post_img' => $this->img($post->post_img),
            'published_at' => $post->published_at,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'category' => $post->category->name,
            'author' => $post->user ? $post->user->name : null,
            'img_alt' => $this->img_alt,
          ];
        }
        return $posts;
    }
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'posts' => $this->post($this->posts()->where('published', 0)
                                                        ->orderBy('created_at', 'DESC')
                                                       ->limit(4)
                                                        ->get()),
        ];
    }
}

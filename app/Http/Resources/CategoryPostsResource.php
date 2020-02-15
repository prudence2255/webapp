<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPostsResource extends JsonResource
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
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'post_title' => $this->post_title,
            'post_img' => $this->img($this->post_img),
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => $this->category->name,
            'author' => $this->user ? $this->user->name : null,
            'img_alt' => $this->img_alt,
        ];
    }
}

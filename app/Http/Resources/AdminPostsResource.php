<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tag;

class AdminPostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   


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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'published' => $this->published,
        ];
    }
}

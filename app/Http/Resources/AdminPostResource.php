<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tag;

class AdminPostResource extends JsonResource
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
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'post_title' => $this->post_title,
            'post_content' => $this->post_content,
            'post_img' => $this->img($this->post_img),
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => $this->category,
            'author' => $this->user ? $this->user->name : null,
            'published' => $this->published,
            'post_tags' => $this->tags,
            'img_alt' => $this->img_alt,
            'source' => $this->source,
            'description' => $this->description,
        ];
    }
}

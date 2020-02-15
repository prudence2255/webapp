<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tag;
use App\Category;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
   
    protected $fillable = [
        'post_title', 'post_content', 'post_img', 'category_id', 'user_id', 
        'published', 'published_at', 'files', 'img_alt', 'source', 'description'

    ];

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function delete_image() {
        if($this->post_img){
            $urls = $this->post_img;
            $paths = explode("|", $urls);
                foreach ($paths as $path) {
                  $url = parse_url($path);
                  if(file_exists(public_path($url['path']))){
                    unlink(public_path($url['path']));
                  }
                }
        }
       
    }

    public function delete_file() {
       
        $files = parse_url($this->files);
        if($this->files){
            $files = $this->files;
           if(strpos($files, "|") !== false){
            $filePaths = explode("|", $files);
            foreach ($filePaths as $filePath) {
              $file = parse_url($filePath);
              if(file_exists(public_path($file['path']))){
                unlink(public_path($file['path']));
              }
              
            }
           
           } else{
               $file = parse_url($this->files);
            unlink(public_path($file['path']));
           
        }
           
           }
    }
}

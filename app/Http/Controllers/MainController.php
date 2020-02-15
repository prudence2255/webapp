<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Post;; 

class MainController extends Controller
{
    public $title;
    public $date;
    public $source;
    public $img;
    public $alt;
    public $article;
    public $description;
    public $status = false;
    public $count = 0;
    public $baseUrl = 'https://cdn.ghanaweb.com/feed/newsfeed.xml';
    public function scrape() {
        $client = new Client();
        $res = $client->request('GET', $this->baseUrl);
        $links = $res->filter('item > link')->each(function($link) {
            return $link->text();
        });
        foreach ($links as $link => $a) {
           $data = $client->request('GET', $a);
           $title = $data->filter('div.article-left-col > h1')->text('No title');
           $date = $data->filter('div.article-left-col > a#date')->text(now());
           $source = $data->filter('div.article-left-col p')->eq(1)->text('enthusiastgh.com');
           if($data->filter('head > meta')->count() > 11){
            $this->description = $data->filter('head > meta')->eq(11)->attr('content');
           }else{
            $this->description = 'No description';
           }
           if($data->filter('p.article-image img')->count() > 0){
            $this->img = $data->filter('p.article-image img')->attr('src');
            $this->alt = $data->filter('p.article-image img')->attr('alt');
           }else{
            $this->img = 'No image';
            $this->alt = 'No alt';
           }
           $article = $data->filter('div.article-left-col p#article-123')->html('<p>No content</p>');
           $isPost = Post::where('post_title', $title)->first();
            $post_img = $this->img.'|'.$this->img;
           if(!$isPost){
            $this->count++;
            $this->status = true;
            $post = Post::create([
                'user_id' => auth()->user()->id,
                'category_id' => 1,
                'post_title' => $title,
                'post_content' => $article,
                'post_img' => $post_img,
                'published_at' => $date,
                'img_alt' => $this->alt,
                'source' => $source,
                 'description' => $this->description,
            ]);
           }             
        }
        if($this->status){
            return response(['message' => $this->count. ' new posts added successfully']);
        }else{
            return response(['message' => 'No new post found']);
      }     
     } 

    
}

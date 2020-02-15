<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Str;

$factory->define(Post::class, function (Faker $faker) {
    $image = $faker->image();
        $imgExt = $image->getClientOriginalExtension();
        $filename = time().'.'.$imgExt;
        $path = $image->move(public_path('posts'), $filename);
        $url = url('posts/'.$filename);
    return [
        'user_id' => function(){
            return App\User::all()->random();
        },
        'category_id' => function(){
            return App\Category::all()->random();
        },
        'post_title' => $faker->word,
        'post_content' => $faker->paragraph,
        'post_img' => $url,
        'published_at' => now(),
        'published' => false,

    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {

    // $image = $faker->image();
    // $imgExt = $image->getClientOriginalExtension();
    // $filename = time().'.'.$imgExt;
    // $path = $image->move(public_path('users'), $filename);
    // $url = url('users/'.$filename);

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'about' => $faker->paragraph,
        'user_img' => 'user.jpg',
        'role' => 'writer',
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

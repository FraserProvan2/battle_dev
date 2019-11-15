<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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
    return [
        'github_id' => $faker->numberBetween(5000, 6000),
        'name' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'avatar' => 'https://avatars1.githubusercontent.com/u/45345543',
        'remember_token' => Str::random(10),
    ];
});

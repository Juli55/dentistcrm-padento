<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt('testtest'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\UserMeta::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'user_id'   => $factory->create(App\User::class)->id,
        'firstname' => $faker->firstName,
        'lastname'  => $faker->lastName
    ];
});

$factory->define(App\Lab::class, function (Faker\Generator $faker) {
    return [
        'user_id' => rand(1,10),
        'name' => $faker->name,
        'status' => 'aktiv',
        'google_city' => $faker->city,
        'lat' => '12.12',
        'lon' => '52.3',
        'directtoken' => $faker->uuid
    ];
});

$factory->define(App\LabMeta::class, function(Faker\Generator $faker) use ($factory) {
    return [
        'lab_id' => $factory->create(App\Lab::class)->id,
        'hello' => $faker->paragraph,
        'contact_person' => $faker->name,
        'special1' => $faker->word,
        'special2' => $faker->word,
        'special3' => $faker->word,
        'special4' => $faker->word,
        'special5' => $faker->word,
        'text' => $faker->paragraph(5,false),
        'contact_email' => $faker->email,
        'tel' => $faker->phoneNumber,
        'count' => rand(1,500),
        'street' => $faker->streetAddress,
        'city' => $faker->city,
        'zip' => $faker->postcode
    ];
});

$factory->define(App\Dentist::class, function (Faker\Generator $faker) {
    return [
        'user_id' => rand(1,10),
        'name' => $faker->name,
        'status' => 'aktiv',
        'google_city' => $faker->city,
        'lat' => '12.12',
        'lon' => '52.3',
        'directtoken' => $faker->uuid
    ];
});

$factory->define(App\DentMeta::class, function(Faker\Generator $faker) use ($factory){
    return [
        'lab_id' => $factory->create(App\Dentitst::class)->id,
        'hello' => $faker->paragraph,
        'contact_person' => $faker->name,
        'special1' => $faker->word,
        'special2' => $faker->word,
        'special3' => $faker->word,
        'special4' => $faker->word,
        'special5' => $faker->word,
        'text' => $faker->paragraph(5,false),
        'contact_email' => $faker->email,
        'tel' => $faker->phoneNumber,
        'count' => rand(1,500),
        'street' => $faker->streetAddress,
        'city' => $faker->city,
        'zip' => $faker->postcode
    ];
});

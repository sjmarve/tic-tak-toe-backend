<?php

use Faker\Generator as Faker;

$factory->define(App\Game::class, function (Faker $faker) {
    return [
        'group_identifier' => $faker->uuid,
    ];
});

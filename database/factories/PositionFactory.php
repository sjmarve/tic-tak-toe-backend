<?php

use App\Game;
use Faker\Generator as Faker;

$factory->define(App\Position::class, function (Faker $faker) {
    return [
        'game_id' => function($faker){
            return factory(Game::class)->create()->id;
        },
        'position' => '---------'
    ];
});

<?php

namespace Tests\Feature;

use App\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * test index listing of games
     *
     * @return void
     */
    public function testGamesIndex()
    {
        $this->withoutExceptionHandling();
        factory(Game::class, 10)->create(['group_identifier' => 1234])->each(function($g){
            $g->positions()->create(['data'=> 'xo-------']);
        });
        $response = $this->json('get', '/api/games?group_identifier=1234');

        $response->assertStatus(200);
    }

    /**
     * test game entry
     *
     * @return void
     */
    public function testGameStart()
    {
        $this->withoutExceptionHandling();

        $response = $this->json('post', '/api/games', [
            'group_identifier' => '2345',
            'position' =>'---------',
            'square' => 1
        ]);
        // dd($response->json());
        $response->assertStatus(200);
    }

    /**
     * test game progression
     *
     * @return void
     */
    public function testGameUpdate()
    {
        $this->withoutExceptionHandling();

        $game = factory(Game::class)->create();
        $game->positions()->create([
            'data' =>'xo-------',
        ]);

        $response = $this->json('patch', '/api/games/'.$game->id, [
            'position' =>'xo-------',
            'square' => 4,
        ]);
        // dd($response->json());
        $response->assertStatus(200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Game;
use App\Http\Resources\GameResource;
use App\Position;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validData = request()->validate([
            'group_identifier' => 'required|string',
        ]);
        //get all games for this identifier
        $games = Game::with('positions')
            ->where('group_identifier', $validData['group_identifier'])
            ->get();

        return GameResource::collection($games);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validData = $request->validate([
            'group_identifier' => 'required|string',
            'position'         => 'required|regex:/^[-]{9}$/',
            'square'           => 'required|integer|between:1,9',
        ]);

        $game = Game::create([
            'group_identifier' => $validData['group_identifier'],
        ]);

        $position = Position::create([
            'data'    => $validData['position'],
            'game_id' => $game->id,
        ]);

        //square is 1-9 but position is 0 based
        $position->processMove($validData['square'] - 1);

        return new GameResource($game->fresh());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {
        $validData = $request->validate([
            'position' => 'required|regex:/^[xo-]{9}$/',
            'square'   => 'required|integer|between:1,9',
        ]);

        $position = Position::make([
            'game_id' => $game->id,
            'data' => $validData['position'],
        ]);

        //square is 1-9 but position is 0 based
        $position->processMove($validData['square'] - 1);

        return new GameResource($game->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        return $game->delete();
    }

}

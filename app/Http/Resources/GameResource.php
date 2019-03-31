<?php

namespace App\Http\Resources;

use App\Http\Resources\PositionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lastPosition = $this->positions->last();
        return [
            'id'               => $this->id,
            'group_identifier' => $this->group_identifier,
            'result'           => $this->result,
            'last_position'    => $lastPosition->data,
            'next_to_play'     => $lastPosition->player,
            'created_at'       => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at->format('Y-m-d H:i:s'),
            'game_progression' => PositionResource::collection($this->positions),
        ];
    }
}

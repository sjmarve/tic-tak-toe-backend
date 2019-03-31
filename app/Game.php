<?php

namespace App;

use App\Position;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $guarded = [];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}

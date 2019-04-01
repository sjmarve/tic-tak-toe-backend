<?php

namespace App;

use App\Game;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    //assumes comp will always be o
    protected $player = 'o';

    protected $guarded = [];

    /**
     * Function to process the next move on the current position
     *
     * @param  integer $square The square that was passed in
     *
     * @return void
     */
    public function processMove($square)
    {
        //only if move is valid
        if($this->board[$square] == '-') {
            //Check whether the game is over
            if($this->terminal()) {
                $this->save();
                $this->endgame();
            }else {
                //Check new game and if still playing, get next move.
                $board = $this->board;
                $board[$square] = "x";
                $this->data = implode("", $board);

                if ($this->terminal()) {
                    $this->save();
                    $this->endgame();
                } else {
                    $moves = $this->nextStates();
                    $min = 2;
                    $next = $moves[0];
                    foreach ($moves as $pos) {
                        $curr = $pos->minimax();
                        if ($curr >= $min) {
                            $next = $pos;
                            $min = $curr;
                        }
                    }
                    $next->save();
                    if ($next->terminal()) {
                        $next->endgame();
                    }
                }
            }
        }
    }

    /**
     * Relationship with the Game
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo::class
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Determine if the game is over
     * @return void
     */
    public function endgame() {
        $result = $this->win();
        if ($result == 1) {
            $this->game->update([
                'result' => 'x won',
            ]);
        } else if ($result == -1) {
            $this->game->update([
                'result' => 'o won',
            ]);
        } else {
            $this->game->update([
                'result' => 'draw',
            ]);
        }
    }

    /**
     * Transform position to array
     * @return array The position string split into an array
     */
    public function getBoardAttribute(){
        // if(is_array($this->data)) dd($this->data);
        return str_split($this->data);
    }

    /**
     *  Possible continuation Positions for the current position.
     * @return array Position
     */
    public function nextStates()
    {
        $game = $this->game;
        $next = [];
        foreach ($this->board as $index => $square) {
            if ($square == "-") {
                $nextPosition = Position::make([
                    'data'    => $this->makeMove($index),
                    'game_id' => $game->id,
                ]);
                $nextPosition->switchPlayer();
                $next[] = $nextPosition;
            }
        }
        return $next;
    }

    /**
     * Switch player
     * @return char The switched Player
     */
    public function switchPlayer()
    {
        $this->player = $this->player == 'o' ? 'x' : 'o';
    }

    public function maxPlayer(){
        return ($this->player == "x");
    }

    /**
     * Alter the current position state
     * @param  integer $square The square to process
     * @return false|array Returning the position array if possible or false if not.
     */
    public function makeMove($square)
    {
        if ($this->board[$square] == "-") {
          $newPosition = $this->board;
          $newPosition[$square] = $this->player;
          return implode("", $newPosition);
        } else {
            return false;
        }
    }

    /**
     * Check whether the game is over - no more moves/win
     * @return bool true/false
     */
    public function terminal()
    {
        return $this->win() != 0 || false === array_search('-', $this->board);
    }

    /**
     * Check if the position is a win/draw/loss using all the lines
     * @return int win/draw/loss
     */
    public function win()
    {
        $lines = [
            [
                $this->board[0],
                $this->board[4],
                $this->board[8]
            ],
            [
                $this->board[2],
                $this->board[4],
                $this->board[6]
            ],
        ];

        for($i=0; $i<=2; $i++) {
            $lines[] = [
                $this->board[$i],
                $this->board[$i+3],
                $this->board[$i+6]
            ];
            $lines[] = [
                $this->board[$i*3],
                $this->board[$i*3+1],
                $this->board[$i*3+2]
            ];
        }

        foreach ($lines as $line) {
            if ($this->checkline($line)) {
                if ($line[0] == "o") {
                    return -1;
                } else if ( $line[0] == "x" ) {
                    return 1;
                }
            }
        }

        return 0;
    }

    /**
     * Check whether line is a winning line or not
     *
     * @param  string $line The board position as a string
     *
     * @return bool Whether ther is a winner or not
     */
    public function checkline($line)
    {
        return ((!($line[0] == "-")) && ($line[0] == $line[1]) && ($line[1] == $line[2]));
    }

    // minimax theory implimentation
    // https://en.wikipedia.org/wiki/Minimax
    public function minimax()
    {
      if ($this->maxPlayer()) {
        return $this->maxvalue(-2, 2);
      } else {
        return $this->minvalue(-2, 2);
      }
    }

    public function maxvalue($alpha, $beta)
    {
        if ($this->terminal()) {
            return $this->win();
        } else {
            $v = -2;
            foreach($this->nextStates() as $success) {
                $v = max($v, $success->minvalue($alpha, $beta));
                if($v >= $beta) {
                    return $v;
                }
                $alpha = max($alpha, $v);
            }
            return $v;
        }
    }

    public function minvalue($alpha, $beta)
    {
        if ($this->terminal()) {
            return $this->win();
        } else {
            $v = 2;
            foreach($this->nextStates() as $success) {
                $v = min($v, $success->maxvalue($alpha, $beta));
                if ($v <= $alpha) {
                    return $v;
                }
                $beta = min($beta, $v);
            }
            return $v;
        }
    }

}

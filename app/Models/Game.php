<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'team_set_id',
        'team_1',
        'team_2',
        'score_1',
        'score_2',
        'winner',
    ];

    public function teamSet()
    {
        return $this->belongsTo(TeamSet::class);
    }
}
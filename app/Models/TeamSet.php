<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamSet extends Model
{
    protected $fillable = ['name', 'played_at'];

    public function players()
    {
        return $this->hasMany(TeamSetUser::class);
    }
}

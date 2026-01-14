<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamSetUser extends Model
{
    protected $fillable = ['team', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teamSet()
    {
        return $this->belongsTo(TeamSet::class);
    }
}

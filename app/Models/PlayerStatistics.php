<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerStatistics extends Model
{
    protected $fillable = [
        'player_id',
        'global_rank',
        'country_rank',
        'pp',
    ];
}

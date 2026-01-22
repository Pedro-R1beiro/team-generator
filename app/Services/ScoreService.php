<?php

namespace App\Services;

class ScoreService
{
    const WIN_POINTS = 3;

    const LOSS_POINTS = 1;

    public static function calculate(int $played, int $wins): int
    {
        return ($wins * self::WIN_POINTS)
             + (($played - $wins) * self::LOSS_POINTS);
    }
}
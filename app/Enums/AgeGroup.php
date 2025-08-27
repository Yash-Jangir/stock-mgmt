<?php

namespace App\Enums;

enum AgeGroup: string
{
    case ADULT = 'adult';
    case KIDS  = 'kids';

    public function label(): string
    {
        return match($this) {
            self::ADULT => 'Adult',
            self::KIDS  => 'Kids',
        };
    }
}

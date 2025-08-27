<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case UNISEX = 'unisex';

    public function label(): string
    {
        return match($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::UNISEX => 'Unisex',
        };
    }
}

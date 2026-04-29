<?php

namespace App\Enums;

enum ItemUnit: string
{
    case Kg = 'kg';
    case Gram = 'gram';
    case Litre = 'litre';
    case Piece = 'piece';

    public function label(): string
    {
        return match ($this) {
            self::Kg => 'kg',
            self::Gram => 'gram',
            self::Litre => 'litre',
            self::Piece => 'piece',
        };
    }
}

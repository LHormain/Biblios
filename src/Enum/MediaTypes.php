<?php

namespace App\Enum;

enum MediaTypes: string
{
    case Book = 'book';
    case Film = 'film';
    case Music = 'music';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::Book => 'Livre',
            self::Film => 'Film',
            self::Music => 'Musique',
        };
    }
}
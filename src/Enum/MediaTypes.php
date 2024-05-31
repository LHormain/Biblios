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

    public static function getAssociatedArray(): array  // crée un array pour afficher la liste complète
    {
        $to_return = [];
        foreach (self::cases() as $status) {
            $to_return[$status->name] = $status;
        }

        return $to_return;
    }
}
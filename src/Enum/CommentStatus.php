<?php

namespace App\Enum;

enum CommentStatus: string
{
    case Pending = 'En attente';
    case Published = 'Publié';
    case Moderated = 'Modéré';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Published => 'Publié',
            self::Moderated => 'Modéré',
        };
    }
}
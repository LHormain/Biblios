<?php

namespace App\Enum;

enum BookCategories: string
{
    case Adventure = 'Adventure';
    case ArtAndMusic = 'ArtAndMusic';
    case Biography = 'Biography';
    case Business = 'Business';
    case ChildrenLiterature = 'ChildrenLiterature';
    case Classics = 'Classics';
    case Comics = 'Comics';
    case ComputersAndTech = 'ComputersAndTech';
    case Cooking = 'Cooking';
    case Fantasy = 'Fantasy';
    case Fiction = 'Fiction';
    case GraphicNovel = 'GraphicNovel';
    case History = 'History';
    case HobbiesAndCrafts = 'HobbiesAndCrafts';
    case HomeAndGarden = 'HomeAndGarden';
    case Horror = 'Horror';
    case Manga = 'Manga';
    case Medical = 'Medical';
    case Mysteries = 'Mysteries';
    case NonFiction = 'NonFiction';
    case Poetry = 'Poetry';
    case Religion = 'Religion';
    case Romance = 'Romance';
    case ScienceFiction = 'ScienceFiction';
    case ScienceAndMath = 'ScienceAndMath';
    case SocialScience = 'SocialScience';
    case Sport = 'Sport';
    case Thriller = 'Thriller';
    case YoungAdult = 'YoungAdult';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::Adventure => 'Aventures',
            self::ArtAndMusic => 'Art & musique',
            self::Biography => 'Biographies',
            self::Business => 'Business',
            self::ChildrenLiterature => 'Littérature jeunesse',
            self::Classics => 'Classiques',
            self::Comics => 'Comics',
            self::ComputersAndTech => 'Informatique et technologie',
            self::Cooking => 'Cuisine',
            self::Fantasy => 'Fantasie',
            self::Fiction => 'Fiction',
            self::GraphicNovel => 'Bande dessinées',
            self::History => 'Histoire',
            self::HobbiesAndCrafts => 'Loisirs créatifs',
            self::HomeAndGarden => 'Maison & Jardin',
            self::Horror => 'Horreur',
            self::Manga => 'Manga, manhwa & manhua',
            self::Medical => 'Médical',
            self::Mysteries => 'Policier',
            self::NonFiction => 'Ouvrages généraux',
            self::Poetry => 'Poésie',
            self::Religion => 'Religion',
            self::Romance => 'Romance',
            self::ScienceFiction => 'Science fiction',
            self::ScienceAndMath => 'Science & mathématiques',
            self::SocialScience => 'Sciences sociales',
            self::Sport => 'Sport',
            self::Thriller => 'Thriller',
            self::YoungAdult => 'Jeune adulte',
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